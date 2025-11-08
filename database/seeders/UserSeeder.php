<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $superAdminRole = Role::where('role_name', 'Super Admin')->first();
        $companyAdminRole = Role::where('role_name', 'Company Admin')->first();
        $contributorRole = Role::where('role_name', 'Contributor')->first();
        $viewerRole = Role::where('role_name', 'Viewer')->first();

        // Get organizations
        $headquarters = Organization::where('org_number', 'ORG-001')->first();
        $salesDept = Organization::where('org_number', 'ORG-002')->first();
        $itDept = Organization::where('org_number', 'ORG-003')->first();
        $hrDept = Organization::where('org_number', 'ORG-004')->first();

        // Create Super Admin
        $superAdmin = User::create([
            'full_name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'ic_number' => '900101-01-0001',
            'position' => 'Chief Executive Officer',
            'race' => 'Malay',
            'nationality' => 'Malaysian',
            'gender' => 'Male',
            'is_active' => "Y",
            'login_method' => 'email',
            'is_change_password' => "Y",
            'role_guid' => $superAdminRole->id,
            'password_changed_at' => now(),
            'last_login_at' => now(),
        ]);
        $superAdmin->organizations()->attach($headquarters->id);

        // Create Company Admin for Sales Department
        $salesAdmin = User::create([
            'full_name' => 'Ahmad bin Abdullah',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'ic_number' => '850505-05-0002',
            'position' => 'Sales Director',
            'race' => 'Malay',
            'nationality' => 'Malaysian',
            'gender' => 'Male',
            'is_active' => "Y",
            'login_method' => 'email',
            'is_change_password' => "Y",
            'role_guid' => $companyAdminRole->id,
            'password_changed_at' => now(),
            'last_login_at' => now(),
        ]);
        $salesAdmin->organizations()->attach($salesDept->id);

        // Create Company Admin for IT Department
        $itAdmin = User::create([
            'full_name' => 'Lee Mei Ling',
            'email' => 'admin.it@example.com',
            'password' => Hash::make('password'),
            'ic_number' => '880808-08-0003',
            'position' => 'IT Director',
            'race' => 'Chinese',
            'nationality' => 'Malaysian',
            'gender' => 'Female',
            'is_active' => "Y",
            'login_method' => 'email',
            'is_change_password' => "Y",
            'role_guid' => $companyAdminRole->id,
            'password_changed_at' => now(),
            'last_login_at' => now(),
        ]);
        $itAdmin->organizations()->attach($itDept->id);

        // Create Contributor for Sales
        $salesContributor = User::create([
            'full_name' => 'Siti Nurhaliza',
            'email' => 'company@example.com',
            'password' => Hash::make('password'),
            'ic_number' => '900303-03-0004',
            'position' => 'Sales Manager',
            'race' => 'Malay',
            'nationality' => 'Malaysian',
            'gender' => 'Female',
            'is_active' => "Y",
            'login_method' => 'email',
            'is_change_password' => "Y",
            'role_guid' => $contributorRole->id,
            'password_changed_at' => now(),
            'last_login_at' => now(),
        ]);
        $salesContributor->organizations()->attach($salesDept->id);

        // Create Contributor for IT
        $itContributor = User::create([
            'full_name' => 'Raj Kumar',
            'email' => 'contributor.it@example.com',
            'password' => Hash::make('password'),
            'ic_number' => '870707-07-0005',
            'position' => 'IT Manager',
            'race' => 'Indian',
            'nationality' => 'Malaysian',
            'gender' => 'Male',
            'is_active' => "Y",
            'login_method' => 'email',
            'is_change_password' => "Y",
            'role_guid' => $contributorRole->id,
            'password_changed_at' => now(),
            'last_login_at' => now(),
        ]);
        $itContributor->organizations()->attach($itDept->id);

        // Create Viewers
        $viewer1 = User::create([
            'full_name' => 'Tan Wei Jie',
            'email' => 'viewer1@example.com',
            'password' => Hash::make('password'),
            'ic_number' => '950606-06-0006',
            'position' => 'Sales Executive',
            'race' => 'Chinese',
            'nationality' => 'Malaysian',
            'gender' => 'Male',
            'is_active' => "Y",
            'login_method' => 'email',
            'is_change_password' => "Y",
            'role_guid' => $viewerRole->id,
            'password_changed_at' => now(),
            'last_login_at' => now(),
        ]);
        $viewer1->organizations()->attach($salesDept->id);

        $viewer2 = User::create([
            'full_name' => 'Priya Nair',
            'email' => 'viewer2@example.com',
            'password' => Hash::make('password'),
            'ic_number' => '920404-04-0007',
            'position' => 'Software Developer',
            'race' => 'Indian',
            'nationality' => 'Malaysian',
            'gender' => 'Female',
            'is_active' => "Y",
            'login_method' => 'email',
            'is_change_password' => "Y",
            'role_guid' => $viewerRole->id,
            'password_changed_at' => now(),
            'last_login_at' => now(),
        ]);
        $viewer2->organizations()->attach($itDept->id);

        $viewer3 = User::create([
            'full_name' => 'Nurul Aisyah',
            'email' => 'viewer3@example.com',
            'password' => Hash::make('password'),
            'ic_number' => '960909-09-0008',
            'position' => 'HR Officer',
            'race' => 'Malay',
            'nationality' => 'Malaysian',
            'gender' => 'Female',
            'is_active' => "Y",
            'login_method' => 'email',
            'is_change_password' => "Y",
            'role_guid' => $viewerRole->id,
            'password_changed_at' => now(),
            'last_login_at' => now(),
        ]);
        $viewer3->organizations()->attach($hrDept->id);
    }
}
