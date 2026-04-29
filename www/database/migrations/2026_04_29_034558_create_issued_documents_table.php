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
        Schema::create('issued_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('document_template_id')->nullable();
            
            $table->string('reference_code')->unique(); // Ex: DOC-2026-0001
            $table->longText('content'); // Conteúdo processado congelado
            $table->enum('status', ['valid', 'rectified', 'cancelled'])->default('valid');
            
            $table->unsignedBigInteger('rectified_by_id')->nullable(); // Aponta para o novo documento caso retificado
            $table->unsignedBigInteger('issued_by_id')->nullable(); // Usuário gerador
            
            $table->timestamps();
            $table->softDeletes();
            
            // Relacionamentos
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('document_template_id')->references('id')->on('document_templates')->onDelete('set null');
            $table->foreign('rectified_by_id')->references('id')->on('issued_documents')->onDelete('set null');
            $table->foreign('issued_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issued_documents');
    }
};
