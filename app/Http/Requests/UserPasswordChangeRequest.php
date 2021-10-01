<?php


namespace App\Http\Requests;


class UserPasswordChangeRequest extends Request
{
    public function rules()
    {
        return [
            'current_password' =>   ['required', 'string', 'min:8'],
            'new_password' =>       ['required', 'string', 'min:8'],
        ];
    }
}
