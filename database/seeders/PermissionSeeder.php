<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tag;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    private $models = [
        'user',
        'tag',
        'meeting',
        'notification',
        'role',
        'permission',
        'popup',
    ];

    private $actions = ['get_all', 'get', 'create', 'update', 'delete'];

    private $customPermissions = [
        'user' => ['reset_password', 'get_all_roles', 'submit_payment_request'],
        'tag' => ['attach', 'detach'],
        'notification' => ['send', 'read'],
        'meeting' => ['get_all_users', 'add_user', 'remove_user'],
        'role' => ['get_all_users', 'add_user', 'remove_user', 'get_all_permissions', 'add_permission', 'remove_permission', 'clone'],
        'popup' => ['add_meeting']
    ];

    private $rolePermissions = [
        'member' => [
            'models' => [
                'notifications' => [
                    'all' => true
                ],
            ]
        ],
        // 'developer' => [
        //     'extends' => 'member',
        //     'models' => [
        //         'payment_request' => [
        //             'only' => ['get_all', 'get', 'create']
        //         ],
        //         'meeting_request' => [
        //             'only' => ['create']
        //         ],
        //     ]
        // ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->models as $model) {
            $tag = Tag::firstOrCreate(['name' => "{$model}_permissions"]);
            foreach ($this->actions as $action) {
                $permission = Permission::create(['name' => Str::of("{$action}_{$model}")->upper()]);
                $permission->tags()->attach($tag);
            }
        }

        foreach ($this->customPermissions as $model => $actions) {
            $tag = Tag::firstOrCreate(['name' => "{$model}_permissions"]);
            foreach ($actions as $action) {
                $permission = Permission::create(['name' => Str::of("{$action}_{$model}")->upper()]);
                $permission->tags()->attach($tag);
            }
        }

        foreach ($this->rolePermissions as $role => $permissions) {
            foreach ($permissions as $permissionType => $permissionValue) {
                switch ($permissionType) {
                    case 'models':
                        foreach ($permissionValue as $model => $accesses) {
                            foreach ($accesses as $accessType => $actions) {
                                switch ($accessType) {
                                    case 'only':
                                        foreach ($actions as $action) {
                                            $permission = Permission::whereName(Str::of("{$action}_{$model}")->upper())->first();
                                            $permission->assignRole($role);
                                        }
                                        break;
                                    case 'all':
                                        $role = Role::whereName($role)->first();
                                        $permissions = Permission::where('name', 'like', "%_{$model}")->pluck('name');
                                        $role->givePermissionTo($permissions);
                                        break;
                                }
                            }
                        }
                        break;
                    case 'extends':
                        $extendableRolerole = Role::whereName($permissionValue)->first();
                        $rolePermissions = $extendableRolerole->getAllPermissions()->pluck('name');
                        $role = Role::whereName($role)->first();
                        $role->givePermissionTo($rolePermissions);
                        break;
                }
            }
        }
    }
}
