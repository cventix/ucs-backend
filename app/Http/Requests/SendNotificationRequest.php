<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SendNotificationRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $via = request()->has('via') ? request('via') : [];

        return [
            'tags' => ['array', 'required_if:userIds,null'],
            'tags.*' => ['string', 'exists:tags,name'],
            'userIds' => ['array', 'required_if:tags,null'],
            'userIds.*' => ['integer', 'exists:users,id'],
            'via' => ['array', 'required'],
            'via.*' => ['string', 'in:short_message,mail'],
            'delay' => ['date', 'nullable'],
            'message' => ['required', 'string', 'max:255'],
            'subject' => ['string', 'max:100', Rule::requiredIf(in_array('email', $via))],
            'sms_pattern' => ['string', Rule::requiredIf(in_array('short_message', $via))],
        ];
    }
}
