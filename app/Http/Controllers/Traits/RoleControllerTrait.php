<?php

namespace App\Http\Controllers\Traits;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\PermissionRoleRequest;
use App\Http\Requests\RoleUserRequest;
use App\Transformers\UserPartialTransformer;
use Illuminate\Http\JsonResponse;

trait RoleControllerTrait
{
    /**
     * Get Role Permissions.
     *
     * @param Role $role Role.
     *
     * @return JsonResponse
     */
    public function getPermissions(Role $role)
    {
        $permissions = $role->permissions()->transformIt();

        return $this->successResponse($permissions);
    }

    /**
     * Add Role Permission.
     *
     * @param Role $role Role.
     * @param PermissionRoleRequest $request Request.
     *
     * @return JsonResponse
     */
    public function postPermission(Role $role, PermissionRoleRequest $request)
    {
        $role->givePermissionTo($request->permissions);

        return $this->successResponse();
    }

    /**
     * Remove Role Permission.
     *
     * @param Role $role Role.
     * @param Permission $permission Permission.
     *
     * @return JsonResponse
     */
    public function deletePermission(Role $role, Permission $permission)
    {
        $role->revokePermissionTo($permission->id);

        return $this->successResponse();
    }

    /**
     * Get Role Users.
     *
     * @param Role $role Role.
     *
     * @return JsonResponse
     */
    public function getUsers(Role $role)
    {
        $users = $role->users()->transformIt(new UserPartialTransformer());

        return $this->successResponse($users);
    }

    /**
     * Add Role Users.
     *
     *
     * @param Role $role Role.
     * @param RoleUserRequest $request Request.
     *
     * @return JsonResponse
     */
    public function postUser(Role $role, RoleUserRequest $request)
    {
        foreach ($request->users as $userId) {
            $user = User::find($userId);
            $user->assignRole($role);
        }

        return $this->successResponse();
    }

    /**
     * Remove Role Users.
     *
     * @param Role $role Role.
     * @param User $user User.
     *
     * @return JsonResponse
     */
    public function deleteUser(Role $role, User $user)
    {
        $user->removeRole($role);

        return $this->successResponse();
    }

    public function clone(Role $role)
    {
        $role->clone();
        return $this->successResponse();
    }
}
