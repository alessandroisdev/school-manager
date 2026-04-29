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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete()->after('status');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('bank_account_id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropColumn(['bank_account_id', 'discount_percentage', 'discount_amount']);
        });
    }
};
