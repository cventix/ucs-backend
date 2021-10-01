<?php

namespace App\Http\Requests;

use App\Models\Role;

/**
 * Class RoleRequest
 *
 * @package App\Http\Requests
 */
class RoleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => sprintf(
                'required|unique:%s,%s,%s',
                'roles',
                'title',
                optional($this->role)->getId()
            ),
        ];
    }
}
