<?php


namespace App\Http\Requests;


use App\Models\User;

class UserAvatarRequest extends Request
{
    public function rules()
    {
        return (new User())->getMediaFieldsRules(true);
    }
}
