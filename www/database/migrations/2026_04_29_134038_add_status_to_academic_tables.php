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
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('assigned_workload')->comment('draft, published');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('day_of_week')->comment('draft, published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
