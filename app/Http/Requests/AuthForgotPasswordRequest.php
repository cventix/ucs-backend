<?php


namespace App\Http\Requests;


class AuthForgotPasswordRequest extends Request
{
    public function rules()
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}
