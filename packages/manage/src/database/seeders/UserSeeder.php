<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Deyji\Manage\Models\Privilege\Role;
use Deyji\Manage\Models\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed the database with roles
        $admin_user = Users::create([
            'name' => "Admin",
            'email' => "nibbleadmin@nibble.dev",
            'password' => Hash::make('defaultpassword123'),
            // Emulate email verification
            "verification_token"=>hash_hmac('sha256', Str::random(40), "123")
        ]);

        $sample_guest = Users::create([
            'name' => "Guest",
            'email' => "guest@nibble.dev",
            'password' => Hash::make('guest'),
            // Emulate email verification
            "verification_token"=>hash_hmac('sha256', Str::random(40), "123")
        ]);

        // Check if Role was seeded 
        if(Role::all()){
            $admin_user->assignRole('Admin');
            $sample_guest->assignRole('User');
        }else{
            // If it wasn't
            $user_role = Role::create([
                'name'=>"User",
                'guard_name'=>"api"
            ]);
    
            $admin_role = Role::create([
                'name'=>"Admin",
                'guard_name'=>"api"
            ]);

            $admin_user->assignRole('Admin');
            $sample_guest->assignRole('User');
        }
    }
}
