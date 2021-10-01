<?php


namespace App\Http\Requests;


class TagRequest extends Request
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'without_spaces', 'unique:tags,name'],
        ];
    }
}
