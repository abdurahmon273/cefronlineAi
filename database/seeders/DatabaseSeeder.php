<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
//            PermissionsTableSeeder::class,
//            RolesTableSeeder::class,
//            PermissionRoleTableSeeder::class,
//            UsersTableSeeder::class,
//            RoleUserTableSeeder::class,

        ]);
        for ($i = 1; $i <= 2000; $i++) {
            User::create([
                'name'=>'name'.$i,
                'surname'=>'surname'.$i,
            ]);
        }
    }
}
