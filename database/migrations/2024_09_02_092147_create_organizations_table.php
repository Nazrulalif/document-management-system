<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->string('org_name');
            $table->string('org_number')->nullable();
            $table->date('reg_date')->nullable();
            $table->string('org_address')->nullable();
            $table->string('org_place')->nullable();
            $table->string('nature_of_business')->nullable();
            $table->string('is_operation')->nullable();
            $table->string('is_parent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
