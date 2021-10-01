<?php

namespace App\Http\Requests;

use App\Rules\MeetingUserRule;
use Illuminate\Foundation\Http\FormRequest;

class MeetingUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'integer',
                sprintf('exists:%s,%s', 'users', 'id'),
                new MeetingUserRule($this->meeting)
            ],
        ];
    }
}
