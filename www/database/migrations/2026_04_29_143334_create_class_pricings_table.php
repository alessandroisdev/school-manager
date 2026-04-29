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
        Schema::create('class_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->decimal('annual_amount', 10, 2);
            $table->integer('installments_count')->default(12);
            $table->integer('default_due_day')->default(5);
            $table->timestamps();
            
            $table->unique(['grade_id', 'shift_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_pricings');
    }
};
