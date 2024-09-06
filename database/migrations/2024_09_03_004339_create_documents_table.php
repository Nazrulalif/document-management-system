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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->string('doc_name');
            $table->text('doc_description');
            $table->longText('doc_summary');
            $table->string('doc_type');
            $table->string('doc_author');
            $table->unsignedBigInteger('upload_by');
            $table->unsignedBigInteger('folder_guid');
            $table->unsignedBigInteger('org_guid');
            $table->unsignedBigInteger('tag_guid');
            $table->unsignedBigInteger('latest_version_guid');
            $table->timestamps();

            $table->foreign('upload_by')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('folder_guid')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('org_guid')->references('id')->on('organizations')->onUpdate('cascade');
            $table->foreign('tag_guid')->references('id')->on('tags')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
