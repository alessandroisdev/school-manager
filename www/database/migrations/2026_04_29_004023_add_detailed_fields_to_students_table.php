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
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender', 20)->nullable()->after('birth_date');
            $table->string('blood_type', 5)->nullable()->after('gender');
            $table->text('medical_notes')->nullable()->after('blood_type');
            $table->string('phone', 20)->nullable()->after('medical_notes');
            $table->string('email')->nullable()->after('phone');
            $table->string('address_zipcode', 20)->nullable()->after('email');
            $table->string('address_street')->nullable()->after('address_zipcode');
            $table->string('address_number', 20)->nullable()->after('address_street');
            $table->string('address_neighborhood')->nullable()->after('address_number');
            $table->string('address_city')->nullable()->after('address_neighborhood');
            $table->string('address_state', 2)->nullable()->after('address_city');
            $table->string('status', 20)->default('active')->after('address_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'blood_type', 'medical_notes', 
                'phone', 'email', 'address_zipcode', 
                'address_street', 'address_number', 
                'address_neighborhood', 'address_city', 
                'address_state', 'status'
            ]);
        });
    }
};
