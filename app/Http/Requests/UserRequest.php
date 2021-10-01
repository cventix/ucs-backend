<?php


namespace App\Http\Requests;


use App\Models\User;

class UserRequest extends Request
{
    public function rules()
    {
        return [
            'firstname' => ['string', 'nullable'],
            'lastname' => ['string', 'nullable'],
            'username' => ['required', 'string', 'without_spaces', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'email' => ['required', 'string', 'unique:users,email'],
            'mobile' => ['required', 'string'],
        ];
    }
}
