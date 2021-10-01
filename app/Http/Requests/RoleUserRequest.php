<?php

namespace App\Http\Requests;

use App\Rules\RoleUserRule;

class RoleUserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users' => ['array', 'required'],
            'users.*' => [
                'required',
                'integer',
                sprintf('exists:%s,%s', 'users', 'id'),
                new RoleUserRule($this->role)
            ],
        ];
    }
}
