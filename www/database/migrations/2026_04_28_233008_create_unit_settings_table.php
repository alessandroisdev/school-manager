<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('calculation_rule')->default('simple'); // simple ou weighted
            $table->decimal('passing_grade', 4, 2)->default(6.00);
            $table->decimal('passing_attendance', 5, 2)->default(75.00); // 75.00%
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_settings');
    }
};
