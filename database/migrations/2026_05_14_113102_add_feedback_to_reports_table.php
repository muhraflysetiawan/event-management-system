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
        Schema::table('reports', function (Blueprint $table) {
            $table->text('management_feedback')->nullable()->after('financial_notes');
            $table->foreignId('management_feedback_by')->nullable()->after('management_feedback')->constrained('users')->nullOnDelete();
            $table->dateTime('management_feedback_at')->nullable()->after('management_feedback_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['management_feedback_by']);
            $table->dropColumn(['management_feedback', 'management_feedback_by', 'management_feedback_at']);
        });
    }
};
