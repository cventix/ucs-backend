<?php

namespace App\Http\Requests;

class PermissionRoleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'permissions' => ['array', 'required'],
            'permissions.*' => ['required', 'integer', 'exists:permissions,id'],
        ];
    }
}
