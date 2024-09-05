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
            $table->unsignedBigInteger('doc_guid');
            $table->string('version_number');
            $table->string('file_path');
            $table->string('change_description');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('doc_guid')->references('id')->on('documents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade');
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
