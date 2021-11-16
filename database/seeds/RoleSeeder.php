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
            ['name' => 'admin_opd'],
        ];

        $permissions = [
            ['name' => 'pengaturan-all'],
            ['name' => 'project-read'],
            ['name' => 'project-write'],
            ['name' => 'project-print'],
            ['name' => 'project-verify'],
            ['name' => 'project-payment'],
        ];
        foreach ($permissions as $p) {
            Permission::create($p);
        }

        foreach ($roles as $r) {
            $role = Role::create($r);
            $role = Role::find($role->id);

            if ($r['name'] == 'admin_project') {
                $role->syncPermissions([
                    ['name' => 'project-read'],
                    ['name' => 'project-write'],
                    ['name' => 'project-print'],
                ]);
            } else {
                $role->syncPermissions($permissions);
            }
        }

        $user = User::find(1);

        $user->assignRole('superadmin');
    }
}
