<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => sprintf('required|email|unique:%s,%s', 'users', 'email'),
            'username' => sprintf(
                'required|regex:/[a-zA-Z0-9_@.]{3,}/|unique:%s,%s',
                'users',
                'username'
            ),
            'firstname' => 'required|string|min:2',
            'lastname' => 'required|string|min:2',
            'password' => 'required|string|min:8',
        ];
    }
}
