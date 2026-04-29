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
            // Acadêmico
            $table->string('evaluation_type')->default('bimonthly'); // bimonthly, trimester, semester
            $table->string('attendance_type')->default('daily'); // daily, per_lesson

            // Financeiro
            $table->decimal('late_fee_penalty', 8, 2)->default(2.00); // Multa por atraso (%)
            $table->decimal('discount_before_due', 8, 2)->default(0.00); // Desconto por pontualidade (%)
            $table->string('currency', 3)->default('BRL');

            // Institucional (Branding)
            $table->string('unit_logo')->nullable();
            $table->string('primary_color', 7)->default('#0d6efd');
            $table->text('receipt_header')->nullable();
            $table->text('receipt_footer')->nullable();

            // Integração / Sistema
            $table->string('timezone')->default('America/Sao_Paulo');
            $table->boolean('enable_student_portal')->default(true);
            $table->boolean('enable_teacher_portal')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('unit_settings', function (Blueprint $table) {
            $table->dropColumn([
                'evaluation_type',
                'attendance_type',
                'late_fee_penalty',
                'discount_before_due',
                'currency',
                'unit_logo',
                'primary_color',
                'receipt_header',
                'receipt_footer',
                'timezone',
                'enable_student_portal',
                'enable_teacher_portal'
            ]);
        });
    }
};
