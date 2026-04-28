<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['evaluation_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_entries');
    }
};
