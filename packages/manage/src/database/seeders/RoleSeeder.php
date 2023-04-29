<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Deyji\Manage\Models\Privilege\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed the database with roles
        $user_role = Role::create([
            'name'=>"User",
            'guard_name'=>"api"
        ]);

        $admin_role = Role::create([
            'name'=>"Admin",
            'guard_name'=>"api"
        ]);
    }
}
