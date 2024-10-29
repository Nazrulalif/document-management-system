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
        Schema::create('shared_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_guid')->nullable();
            $table->unsignedBigInteger('org_guid')->nullable();
            $table->timestamps();

            $table->foreign('doc_guid')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('org_guid')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_documents');
    }
};
