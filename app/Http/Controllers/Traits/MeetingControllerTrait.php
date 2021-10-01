<?php

namespace App\Http\Controllers\Traits;

use App\Http\Requests\MeetingUserRequest;
use App\Models\Meeting;
use App\Models\User;
use App\Notifications\Meeting\MeetingNotification;
use App\Transformers\UserPartialTransformer;
use Illuminate\Http\JsonResponse;

trait MeetingControllerTrait
{

    /**
     * Get Meeting Users.
     *
     * @param Meeting $meeting Meeting.
     *
     * @return JsonResponse
     */
    public function getUsers(Meeting $meeting)
    {
        $users = $meeting->users()->transformIt(new UserPartialTransformer());

        return $this->successResponse($users);
    }

    /**
     * Add Meeting User.
     *
     *
     * @param Meeting $meeting Meeting.
     * @param MeetingUserRequest $request Request.
     *
     * @return JsonResponse
     */
    public function postUser(Meeting $meeting, MeetingUserRequest $request)
    {
        $user = User::findOrFail($request->user_id);
        $meeting->users()->attach($user->id);

        $user->notify(new MeetingNotification($meeting));

        return $this->successResponse();
    }

    /**
     * Remove Meeting User.
     *
     * @param Meeting $meeting Meeting.
     * @param User $user User.
     *
     * @return JsonResponse
     */
    public function deleteUser(Meeting $meeting, User $user)
    {
        $meeting->users()->detach([$user->id]);

        return $this->successResponse();
    }
}
