<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->string('folder_name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('parent_folder_guid')->nullable();
            $table->string('is_meeting')->nullable();
            $table->string('is_all_company')->nullable();
            $table->timestamps();


            // Check the database connection type
            $dbDriver = DB::getDriverName();

            if ($dbDriver === 'sqlsrv') {
                // SQL Server constraints
                $table->foreign('parent_folder_guid')->references('id')->on('folders')->onDelete('no action')->onUpdate('no action');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('no action')->onUpdate('no action');
            } elseif ($dbDriver === 'mysql') {
                // MySQL constraints
                $table->foreign('parent_folder_guid')->references('id')->on('folders')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            }
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
