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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Nome de identificação (ex: Itaú Principal)
            $table->string('bank_code', 10); // 341
            $table->string('agency', 20);
            $table->string('account', 20);
            $table->string('wallet', 20)->nullable(); // Carteira
            $table->decimal('fine_percentage', 5, 2)->default(2.00); // Multa %
            $table->decimal('interest_percentage', 5, 2)->default(1.00); // Juros %
            $table->text('instruction_lines')->nullable(); // Instruções do boleto
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
