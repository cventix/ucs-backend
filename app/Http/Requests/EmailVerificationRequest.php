<?php


namespace App\Http\Requests;


class EmailVerificationRequest extends Request
{
    public function rules()
    {
        return [
            'code' => ['required', 'integer', 'min:100000', 'max:999999'],
        ];
    }
}
