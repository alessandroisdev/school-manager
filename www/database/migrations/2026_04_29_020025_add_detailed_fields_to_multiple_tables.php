<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->date('birth_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('description')->nullable();
            $table->string('payment_method')->nullable(); // pix, boleto, credit_card, manual
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'address', 'city', 'state']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'address', 'city', 'state', 'birth_date', 'salary']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['description', 'payment_method']);
        });
    }
};
