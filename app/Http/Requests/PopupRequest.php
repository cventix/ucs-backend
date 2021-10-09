<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PopupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logo' => 'string',
            'title' => 'required|string',
            'description' => 'string',
            'link' => 'required|string|url',
            'duration' => 'integer|min:3000',
            'is_repetitive' => 'boolean',
            'repeat_period' => 'integer|min:2',
            'dimentions' => 'json',
            'styles' => 'json',
        ];
    }
}
