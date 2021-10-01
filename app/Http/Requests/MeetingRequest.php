<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeetingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'user_id'  =>   ['required', 'integer', 'exists:users,id'],
            'started_at' => ['required', 'date', 'after:now'],
            'duration' => ['required', 'integer', 'min:1'],
            'holder' => ['required', 'string', 'in:zoom_meeting,zoom_webinar,google_meet'],
            'attendees' => ['array', 'nullable'],
            'attendees.*' => ['integer', 'exists:users,id']
        ];
    }
}
