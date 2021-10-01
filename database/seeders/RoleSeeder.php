<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    private $defaultRoles = ['super-admin', 'member'];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->defaultRoles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        $user = User::first();
        $user->assignRole('super-admin');
    }
}
