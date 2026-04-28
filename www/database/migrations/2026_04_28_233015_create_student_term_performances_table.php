<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_term_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            
            $table->decimal('calculated_average', 5, 2);
            $table->decimal('attendance_percentage', 5, 2);
            $table->string('status'); // aprovado, reprovado, recuperacao
            
            $table->timestamps();
            
            $table->unique(['student_id', 'subject_id', 'term_id'], 'unique_performance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_term_performances');
    }
};
