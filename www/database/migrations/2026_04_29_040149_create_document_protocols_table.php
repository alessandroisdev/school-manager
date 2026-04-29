<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_protocols', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('protocol_number')->unique();
            $table->string('sender');
            $table->string('subject');
            $table->date('received_date');
            $table->date('due_date')->nullable();
            
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'archived'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            
            $table->text('description')->nullable();
            $table->unsignedBigInteger('received_by_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('received_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_protocols');
    }
};
