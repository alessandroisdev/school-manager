<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->integer('assigned_workload')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Uma professora não deve ser alocada múltiplas vezes para a mesma turma e disciplina.
            $table->unique(['teacher_id', 'school_class_id', 'subject_id'], 'unique_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};
