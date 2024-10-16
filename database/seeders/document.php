<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document as modelDocument;

class document extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        modelDocument::factory()->count(500)->create();
    }
}
