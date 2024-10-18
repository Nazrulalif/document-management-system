<?php

namespace Database\Seeders;

use App\Models\Organization as ModelsOrganization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class organization extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelsOrganization::factory()->count(2000)->create();
    }
}
