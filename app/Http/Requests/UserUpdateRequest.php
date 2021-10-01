<?php


namespace App\Http\Requests;


class UserUpdateRequest extends Request
{
    public function rules()
    {
        return [
            'firstname' =>                  ['string', 'nullable'],
            'lastname' =>                   ['string', 'nullable'],
            'mobile' =>                     ['string', 'nullable'],
            'is_deactivated' =>             ['boolean', 'nullable'],
        ];
    }
}
