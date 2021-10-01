<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
use App\Models\Notification as NotificationModel;
use App\Models\User;
use App\Notifications\Admin\AdminNotification;
use App\Traits\CRUDActions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    use CRUDActions;

    /**
     * TODO: rename it to markAsRead and update permission name
     * 
     * @param Notification $notification Notification.
     * 
     */
    public function readNotification(NotificationModel $notification)
    {
        $notification->read();

        return $this->successResponse();
    }

    /**
     * TODO: rename it to store and update permission name
     * 
     * @param SendNotificationRequest $request
     * @return JsonResponse
     */
    public function sendNotification(SendNotificationRequest $request): JsonResponse
    {
        /** @var User */
        $sender = Auth::user();

        $validated = $request->validated();

        $tags = $request->has('tags') ? $validated['tags'] : [];
        $userIds = $request->has('userIds') ? $validated['userIds'] : [];

        $query = User::query();
        if (!empty($tags))
            $query->withTags($tags);
        if (!empty($userIds))
            $query->whereIn('id', $userIds);

        $targets = $query->get();

        $delay = now();
        if ($request->has('delay'))
            $delay = Carbon::parse($validated['delay']);

        Notification::send($targets, (new AdminNotification($sender, $validated['via'], $validated['message'], $request->subject, $request->sms_pattern))->delay($delay));

        return $this->successResponse();
    }
}
