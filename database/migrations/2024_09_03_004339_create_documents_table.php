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
            $table->id('doc_id');
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
            $table->foreign('folder_guid')->references('folder_id')->on('folders')->onDelete('cascade');
            $table->foreign('org_guid')->references('org_id')->on('organizations')->onUpdate('cascade');
            $table->foreign('tag_guid')->references('tag_id')->on('tags')->onUpdate('cascade');
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
