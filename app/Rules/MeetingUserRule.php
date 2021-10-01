<?php

namespace App\Rules;

use App\Models\Meeting;
use Illuminate\Contracts\Validation\Rule;

class MeetingUserRule implements Rule
{
    /**
     * @var Meeting
     */
    protected Meeting $meeting;

    /**
     * Create a new rule instance.
     *
     * @param Meeting $meeting
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !in_array($value, $this->meeting->users()->pluck('id')->toArray(), true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'User already exists in the meeting!';
    }
}
