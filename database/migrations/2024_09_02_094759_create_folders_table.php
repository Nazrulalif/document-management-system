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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('folder_name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('org_guid')->nullable();
            $table->unsignedBigInteger('parent_folder_guid')->nullable();
            $table->string('is_meeting')->nullable();
            $table->timestamps();

            $table->foreign('parent_folder_guid')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('org_guid')->references('id')->on('organizations')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
