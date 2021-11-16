<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        $roles = [
            ['name' => 'superadmin'],
            ['name' => 'admin_situ'],
            ['name' => 'koordinator'],
        ];

        $permissions = [
            ['name' => 'pengaturan-all'],
            ['name' => 'situ-read'],
            ['name' => 'situ-write'],
            ['name' => 'situ-print'],
            ['name' => 'situ-verify'],
            ['name' => 'situ-skrd-print'],
            ['name' => 'situ-reregistration'],
            ['name' => 'pedagang-read'],
            ['name' => 'pedagang-write'],
            ['name' => 'kios-read'],
            ['name' => 'kios-write'],
        ];
        foreach ($permissions as $p) {
            Permission::create($p);
        }

        foreach ($roles as $r) {
            $role = Role::create($r);
            $role = Role::find($role->id);

            if ($r['name'] == 'admin_situ') {
                $role->syncPermissions([
                    ['name' => 'situ-read'],
                    ['name' => 'situ-write'],
                    ['name' => 'situ-print'],
                    ['name' => 'situ-skrd-print'],
                    ['name' => 'pedagang-read'],
                    ['name' => 'pedagang-write'],
                    ['name' => 'kios-read'],
                    ['name' => 'kios-write'],
                ]);
            } elseif ($r['name'] == 'koordinator') {
                $role->syncPermissions([
                    ['name' => 'situ-read'],
                    ['name' => 'situ-write'],
                    ['name' => 'pedagang-read'],
                    ['name' => 'kios-read'],
                ]);
            } else {
                $role->syncPermissions($permissions);
            }
        }

        $user = User::find(1);

        $user->assignRole('superadmin');
    }
}
