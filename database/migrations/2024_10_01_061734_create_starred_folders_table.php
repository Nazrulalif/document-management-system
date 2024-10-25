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
        Schema::create('starred_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_guid')->nullable();
            $table->unsignedBigInteger('folder_guid')->nullable();
            $table->timestamps();

            //Start: If using Sql Server
            $table->foreign('user_guid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('folder_guid')->references('id')->on('folders')->onDelete('no action')->onUpdate('no action');
            //End: If using Sql Server

            //Start: If using MYSQL
            // $table->foreign('user_guid')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('folder_guid')->references('id')->on('folders')->onDelete('cascade');
            //End: If using MYSQL

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('starred_folders');
    }
};
