<?php


namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdminUser = User::firstOrCreate(
            ['email' => 'super_admin@mail.com'],
            [
                'firstname' => 'super',
                'lastname' => 'admin',
                'username' => 'super_admin',
                'email' => 'super_admin@mail.com',
                'mobile' => '09123456789',
                'password' => Hash::make('123456789'),
            ]
        );
    }
}
