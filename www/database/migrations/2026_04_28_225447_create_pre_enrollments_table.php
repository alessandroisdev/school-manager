<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_enrollments', function (Blueprint $table) {
            $table->id();
            // A captação não exige unidade no momento zero (pode ser o site global), mas pode exigir.
            // Para simplificar, atrelamos à Unidade Sede ou deixamos o admin transferir.
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            
            // Dados do Responsável
            $table->string('parent_name');
            $table->string('email')->nullable();
            $table->string('phone');
            
            // Dados do Aluno
            $table->string('student_name');
            $table->foreignId('grade_id')->nullable()->constrained()->nullOnDelete(); // Série de interesse
            
            $table->text('notes')->nullable();
            
            // pending, approved, rejected
            $table->string('status')->default('pending');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_enrollments');
    }
};
