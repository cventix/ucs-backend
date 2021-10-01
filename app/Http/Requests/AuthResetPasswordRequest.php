<?php


namespace App\Http\Requests;


class AuthResetPasswordRequest extends Request
{
    public function rules()
    {
        return [
            'email' =>              ['required', 'email'],
            'password' =>           ['required', 'min:8'],
            'verification_code' =>  ['required', 'string'],
        ];
    }
}
