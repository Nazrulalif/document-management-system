<?php

namespace Database\Seeders;

use App\Models\Role as ModelsRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Role extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Start the transaction
        DB::transaction(function () {
            // Uncomment the line below if using SQL Server to allow manual ID insertion
            if (DB::getDriverName() === 'sqlsrv') {
                DB::unprepared('SET IDENTITY_INSERT roles ON');
            }

            // Define roles with descriptions
            $roles = [
                'Super Admin' => 'The Super Admin has full access to all system features, including user management, settings configuration, and reports.',
                'Company Admin' => 'The Company Admin manages company-specific settings, oversees user roles, and ensures that operational workflows run smoothly.',
                'Company Contributor' => 'The Company Contributor can create and manage content.',
                'Viewer' => 'The Viewer has read-only access to files and documents, allowing them to stay informed without making changes to the system.',
            ];

            // IDs assigned to each role
            $ids = [1, 2, 3, 4];

            // Insert roles into the database
            foreach ($roles as $roleName => $roleDescription) {
                ModelsRole::create([
                    'id' => array_shift($ids),            // Assign the ID from the $ids array
                    'role_name' => $roleName,             // Set role name
                    'role_description' => $roleDescription,  // Set role description
                ]);
            }

            // Turn off IDENTITY_INSERT after the insertions, if using SQL Server
            if (DB::getDriverName() === 'sqlsrv') {
                DB::unprepared('SET IDENTITY_INSERT roles OFF');
            }
        });
    }
}
