<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('official_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('category_id');
            
            $table->integer('year');
            $table->integer('number');
            $table->string('full_number')->unique(); // Ex: 045/2026/OF
            
            $table->date('date');
            $table->string('recipient')->nullable();
            $table->string('subject');
            $table->longText('content');
            
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');
            
            $table->string('signer_name')->nullable();
            $table->string('signer_title')->nullable();
            $table->string('signature_image_path')->nullable();
            
            $table->unsignedBigInteger('created_by_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('official_document_categories')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
            
            // Impede dois ofícios com mesmo número e ano na mesma categoria
            $table->unique(['unit_id', 'category_id', 'year', 'number'], 'official_docs_unique_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_documents');
    }
};
