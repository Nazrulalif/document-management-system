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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('user_guid');
            $table->string('action');
            $table->unsignedBigInteger('doc_guid');
            $table->dateTime('timestamp');
            $table->timestamps();

            $table->foreign('user_guid')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('doc_guid')->references('doc_id')->on('documents')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
