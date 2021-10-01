<?php


namespace App\Http\Requests;


class AuthLoginRequest extends Request
{
    public function rules()
    {
        return [
            'name' =>       ['nullable', 'string', 'max:250'],
            'username' =>   ['required', 'string'],
            'password' =>   ['required', 'string'],
        ];
    }
}
