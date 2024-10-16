<?php

namespace Database\Seeders;

use App\Models\Role as ModelsRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Role extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin' => 'The Super Admin has full access to all system features, including user management, settings configuration, and reports.',
            'Company Admin' => 'The Company Admin manages company-specific settings, oversees user roles, and ensures that operational workflows run smoothly.',
            'Company Contributor' => 'The Company Contributor can create and manage content.',
            'Viewer' => 'The Viewer has read-only access to files and documents, allowing them to stay informed without making changes to the system.',
        ];

        $ids = [1, 2, 3, 4];

        foreach ($roles as $roleName => $roleDescription) {
            ModelsRole::factory()->create([
                'id' => array_shift($ids),
                'role_name' => $roleName,
                'role_description' => $roleDescription,
            ]);
        }
    }
}
