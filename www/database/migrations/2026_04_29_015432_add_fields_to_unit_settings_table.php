<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_settings', function (Blueprint $table) {
            $table->integer('default_class_capacity')->default(30);
            $table->integer('current_academic_year')->default(date('Y'));
            $table->integer('default_due_day')->default(10);
            $table->decimal('late_fee_interest', 4, 2)->default(2.00);
        });
    }

    public function down(): void
    {
        Schema::table('unit_settings', function (Blueprint $table) {
            $table->dropColumn([
                'default_class_capacity',
                'current_academic_year',
                'default_due_day',
                'late_fee_interest'
            ]);
        });
    }
};
