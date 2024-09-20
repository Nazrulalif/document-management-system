<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Folder as modelFolder;

class folder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        modelFolder::factory()->count(200)->create();
    }
}
