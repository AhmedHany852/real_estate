<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
          
            "users-list",
            "update-user",
            "delete-user",
            "add-user",
            "role",
            "roles-list",
            "update-role",
            "delete-role",
            "add-role",
            "setting",
            "update-or-create-setting",





        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
    
}
