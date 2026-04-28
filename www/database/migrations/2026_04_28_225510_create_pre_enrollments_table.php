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
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained()->cascadeOnDelete();
            $table->string('student_name');
            $table->date('student_birth_date');
            $table->string('guardian_name');
            $table->string('guardian_email')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('status')->default('pendente'); // pendente, aprovada, rejeitada
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_enrollments');
    }
};
