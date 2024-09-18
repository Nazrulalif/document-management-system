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
            $table->string('doc_title')->nullable();
            $table->text('doc_description')->nullable();
            $table->longText('doc_summary')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('doc_author')->nullable();
            $table->string('doc_keyword')->nullable();
            $table->unsignedBigInteger('upload_by')->nullable();
            $table->unsignedBigInteger('folder_guid')->nullable();
            $table->unsignedBigInteger('org_guid')->nullable();
            $table->char('latest_version_guid')->nullable();
            $table->integer('version_limit')->nullable();
            $table->timestamps();

            $table->foreign('upload_by')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('folder_guid')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('org_guid')->references('id')->on('organizations')->onUpdate('cascade');
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
