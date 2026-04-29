<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocol_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('protocol_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable(); // em bytes
            $table->timestamps();

            $table->foreign('protocol_id')->references('id')->on('document_protocols')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocol_attachments');
    }
};
