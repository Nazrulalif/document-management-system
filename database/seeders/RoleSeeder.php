<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'role_name' => 'Super Admin',
                'role_description' => 'Has full system access and can manage all organizations, users, and settings',
            ],
            [
                'role_name' => 'Company Admin',
                'role_description' => 'Can manage users, documents, and folders within their organization',
            ],
            [
                'role_name' => 'Contributor',
                'role_description' => 'Can create, edit, and manage documents and folders',
            ],
            [
                'role_name' => 'Viewer',
                'role_description' => 'Read-only access to documents and folders based on permissions',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
