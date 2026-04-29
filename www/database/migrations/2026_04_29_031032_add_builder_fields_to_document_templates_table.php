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
        Schema::table('document_templates', function (Blueprint $table) {
            $table->foreignId('header_id')->nullable()->after('type')->constrained('document_partials')->nullOnDelete();
            $table->foreignId('footer_id')->nullable()->after('header_id')->constrained('document_partials')->nullOnDelete();
            $table->string('watermark_path')->nullable()->after('footer_id');
            $table->boolean('is_active')->default(true)->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropForeign(['header_id']);
            $table->dropForeign(['footer_id']);
            $table->dropColumn(['header_id', 'footer_id', 'watermark_path', 'is_active']);
        });
    }
};
