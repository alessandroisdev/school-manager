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
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('installment_number')->nullable()->after('amount');
            $table->string('barcode', 100)->nullable()->after('status');
            $table->string('digitable_line', 100)->nullable()->after('barcode');
            $table->text('pix_qr_code')->nullable()->after('digitable_line');
            $table->string('pix_key')->nullable()->after('pix_qr_code');
            $table->foreignId('bank_account_id')->nullable()->after('unit_id')->constrained('bank_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropColumn(['installment_number', 'barcode', 'digitable_line', 'pix_qr_code', 'pix_key', 'bank_account_id']);
        });
    }
};
