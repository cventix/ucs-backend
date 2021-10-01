<?php

namespace App\Policies;

use App\Constants\PermissionTitle;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user User.
     * @param Meeting $meeting Meeting.
     *
     * @return bool
     */
    public function view(User $user, Meeting $meeting): bool
    {
        return $meeting->users()->whereId($user->id)->exists();
    }
}
