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
            $table->id();
            $table->unsignedBigInteger('user_guid')->nullable();
            $table->string('model')->nullable();
            $table->unsignedBigInteger('doc_guid')->nullable();
            $table->string('action')->nullable();
            $table->text('changes')->nullable();
            $table->string('ip_address')->nullable(); // IP address of the user
            $table->timestamps();

            $table->foreign('user_guid')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('doc_guid')->references('id')->on('documents')->onUpdate('cascade')->onDelete('set null');
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
