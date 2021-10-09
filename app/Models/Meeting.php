<?php

namespace App\Models;

use App\Components\SessionHolder\Facade\SessionHolder;
use App\Notifications\Meeting\MeetingNotification;
use App\Traits\MagicMethodsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;

class Meeting extends Model
{
    use HasFactory;
    use MagicMethodsTrait;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'user_id',
        'started_at',
        'duration',
        'holder'
    ];

    protected $hidden = [
        // Todo: temporary hack should be reversed
        // 'holder_session_id',
        'holder_meta',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'holder_meta' => 'array',
    ];

    protected $searchables = [
        'partial' => ['title'],
        'exact' => ['id', 'user_id'],
        'scope' => ['started_at'],
    ];

    protected $sortables = [
        'id', 'started_at', 'created_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_user', 'user_id');
    }

    public function popups()
    {
        return $this->belongsToMany(Popup::class, 'meeting_popup', 'popup_id');
    }

    public function scopeStartedAt($query, $startDate, $endDate = null)
    {
        return $this->dateScopeProvider($query, 'started_at', $startDate, $endDate);
    }

    public static function createGoogleMeetSession(User $host, string $title, $started_at, int $duration)
    {
        return self::create([
            'user_id' => $host->id,
            'title' => $title,
            'started_at' => $started_at,
            'duration' => $duration,
            'holder' => 'google_meet'
        ]);
    }

    protected static function booted()
    {
        parent::booted();

        static::creating(function (Meeting $meeting) {
            $host = User::findOrFail($meeting->user_id);

            $attendees = [];
            if (request()->has('attendees')) {
                // build attendees array
                foreach (request('attendees') as $userId) {
                    $user = User::find($userId);
                    if ($user)
                        array_push($attendees, ['email' => $user->email]);
                }
            }


            $data = SessionHolder::driver($meeting->holder)
                ->host($host)
                ->at($meeting->started_at)
                ->duration($meeting->duration)
                ->title($meeting->title)
                ->meta([
                    'attendees' => $attendees,
                ])
                ->register();

            if (empty($data) || !is_array($data))
                return false;

            $meeting->holder_session_id = $data['id'];
            $meeting->holder_join_url = $data['join_url'];
            $meeting->holder_meta = $data['meta'];
        });

        static::created(function (Meeting $meeting) {
            if (request()->has('attendees')) {
                // Add to meeting users
                $meeting->users()->attach(request('attendees'));
            }
        });

        static::updating(function (Meeting $meeting) {
            // TODO: update attendees in holder_meta
            if ($meeting->isDirty('started_at') || $meeting->isDirty('duration')) {
                $data = SessionHolder::driver($meeting->holder)
                    ->sessionId($meeting->holder_session_id)
                    ->host($meeting->user)
                    ->at($meeting->started_at)
                    ->duration($meeting->duration)
                    ->meta(['meta' => $meeting->holder_meta])
                    ->update();

                if (empty($data) || !is_array($data))
                    return false;

                $meeting->holder_meta = $data['meta'];
            }
        });

        static::deleting(function (Meeting $meeting) {
            SessionHolder::driver($meeting->holder)
                ->sessionId($meeting->holder_session_id)
                ->host($meeting->user)
                ->meta(['meta' => $meeting->holder_meta])
                ->delete();
        });

        static::saved(function (Meeting $meeting) {
            if ($meeting->isDirty('started_at')) {
                $meeting->user->notify(new MeetingNotification($meeting));

                // Send to Meeting Users
                Notification::send($meeting->users, new MeetingNotification($meeting));

                $startedAt = $meeting->started_at;
                $reminders = config('platform.apply.reminders_for_interview_session', []);
                foreach ($reminders as $reminder) {
                    $remindAt = $startedAt->subMinutes($reminder);
                    if ($remindAt->greaterThan(now())) {
                        $meeting->user->notify((new MeetingNotification($meeting))->delay($remindAt));

                        // Send to Meeting Users
                        Notification::send($meeting->users, (new MeetingNotification($meeting))->delay($remindAt));
                    }
                }
            }
        });
    }
}
