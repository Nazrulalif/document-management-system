<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

            $dbDriver = DB::getDriverName();

            if ($dbDriver === 'sqlsrv') {
                $table->foreign('user_guid')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('folder_guid')->references('id')->on('folders')->onDelete('cascade');
            } elseif ($dbDriver === 'mysql') {
                // MySQL constraints
                $table->foreign('user_guid')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('folder_guid')->references('id')->on('folders')->onDelete('cascade');
            }
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
