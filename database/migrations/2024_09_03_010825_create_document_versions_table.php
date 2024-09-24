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
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->string('change_title')->nullable();
            $table->unsignedBigInteger('doc_guid')->nullable();
            $table->string('version_number')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('change_description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->longText('doc_content')->nullable();
            $table->timestamps();

            $table->foreign('doc_guid')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
