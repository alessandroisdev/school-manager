<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0 (Domingo) a 6 (Sábado)
            $table->timestamps();
            $table->softDeletes();

            // Desnormalização estratégica para segurança a nível de BD (Unique Constraints)
            // 1. Um professor não pode dar duas aulas no mesmo horário
            $table->unique(['teacher_id', 'day_of_week', 'time_slot_id'], 'unique_teacher_time');
            
            // 2. Uma turma não pode ter duas aulas diferentes no mesmo horário
            $table->unique(['school_class_id', 'day_of_week', 'time_slot_id'], 'unique_class_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
