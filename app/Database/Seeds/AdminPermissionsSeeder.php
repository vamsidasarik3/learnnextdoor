<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminPermissionsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Add new permissions
        $permissions = [
            ['title' => 'View Listings (Admin)', 'code' => 'listings_view'],
            ['title' => 'Edit Listings (Admin)', 'code' => 'listings_edit'],
            ['title' => 'Manage Users (Admin)', 'code' => 'users_edit'], // might already exist
        ];

        foreach ($permissions as $p) {
            $db->table('permissions')->ignore(true)->insert($p);
        }

        // 2. Link to Admin Role (ID = 1)
        $adminRole = 1;
        $allPerms = $db->table('permissions')->get()->getResult();
        
        foreach ($allPerms as $p) {
            if (in_array($p->code, ['listings_view', 'listings_edit', 'users_edit', 'users_list'])) {
                $db->table('role_permissions')->ignore(true)->insert([
                    'role'       => $adminRole,
                    'permission' => $p->id
                ]);
            }
        }
    }
}
