<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = [
            [
                'org_name' => 'Headquarters',
                'org_number' => 'ORG-001',
                'reg_date' => now()->subYears(5),
                'org_address' => '123 Main Street',
                'org_place' => 'Kuala Lumpur',
                'nature_of_business' => 'Corporate Management',
                'is_operation' => "Y",
                'is_parent' => "Y",
                'org_logo' => null,
            ],
            [
                'org_name' => 'Sales Department',
                'org_number' => 'ORG-002',
                'reg_date' => now()->subYears(3),
                'org_address' => '456 Commerce Avenue',
                'org_place' => 'Petaling Jaya',
                'nature_of_business' => 'Sales and Marketing',
                'is_operation' => "Y",
                'is_parent' => "N",
                'org_logo' => null,
            ],
            [
                'org_name' => 'IT Department',
                'org_number' => 'ORG-003',
                'reg_date' => now()->subYears(4),
                'org_address' => '789 Technology Park',
                'org_place' => 'Cyberjaya',
                'nature_of_business' => 'Information Technology',
                'is_operation' => "Y",
                'is_parent' => "N",
                'org_logo' => null,
            ],
            [
                'org_name' => 'HR Department',
                'org_number' => 'ORG-004',
                'reg_date' => now()->subYears(4),
                'org_address' => '321 People Boulevard',
                'org_place' => 'Shah Alam',
                'nature_of_business' => 'Human Resources',
                'is_operation' => "Y",
                'is_parent' => "N",
                'org_logo' => null,
            ],
        ];

        foreach ($organizations as $organization) {
            Organization::create($organization);
        }
    }
}
