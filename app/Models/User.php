<?php

namespace App\Models;

use App\Traits\MagicMethodsTrait;
use App\Traits\Taggable;
use App\Transformers\UserTransformer;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    MustVerifyEmailContract,
    HasMedia
{
    use Authenticatable, Authorizable, CanResetPassword, HasFactory, MagicMethodsTrait, HasApiTokens, Notifiable, SoftDeletes, InteractsWithMedia, Taggable, HasRoles, HasPushSubscriptions, MustVerifyEmail;

    /** @var array|string[]  */
    public static array $genders = ['MALE', 'FEMALE'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'gender',
        'username',
        'password',
        'email',
        'mobile',
        'is_deactivated',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at'
    ];

    protected $searchables = [
        'exact' => ['id', 'gender'],
        'partial' => ['firstname', 'lastname', 'username', 'email'],
        'scope' => ['created_at', 'with_tag', 'without_tags'],
    ];

    protected $sortables = [
        'id', 'created_at'
    ];

    public function getDefaultTransformer()
    {
        return new UserTransformer();
    }

    /**
     * @return BelongsToMany
     */
    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class);
    }

    /**
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    /**
     * Get the user's fullname.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * User has permission.
     *
     * @param string $permission Permission title.
     *
     * @return boolean
     */
    public function hasPermission(string $permission): bool
    {
        return $this->hasPermissionTo($permission);
    }

    /**
     * Determine user is admin or not
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public static function admins()
    {
        $role = Role::whereName('super-admin')->first();
        return $role->users;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useFallbackUrl('/images/no-avatar.jpg')
            ->useFallbackPath(public_path('/images/no-avatar.jpg'))
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->useDisk('s3')
            ->singleFile();
    }

    protected static function booted()
    {
        parent::booted();

        static::created(function (User $user) {
            Role::firstOrCreate(['name' => 'member']);
            $user->assignRole('member');
        });

        static::deleted(function (User $user) {
            // DeleteUserJob::dispatch($user);
        });
    }
}
