<?php

namespace App\Rules;

use App\Models\Role;
use Illuminate\Contracts\Validation\Rule;

class RoleUserRule implements Rule
{
    /**
     * @var Role
     */
    protected Role $role;

    /**
     * Create a new rule instance.
     *
     * @param Role $role Role.
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
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
        return !in_array($value, $this->role->users()->pluck('id')->toArray(), true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The user already exists in the role.';
    }
}
