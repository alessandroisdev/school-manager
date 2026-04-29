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
        Schema::table('unit_settings', function (Blueprint $table) {
            $table->enum('school_type', ['public', 'private'])->default('public')->after('unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('unit_settings', function (Blueprint $table) {
            $table->dropColumn('school_type');
        });
    }
};
