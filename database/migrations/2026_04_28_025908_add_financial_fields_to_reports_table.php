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
            $table->string('type')->default('overall')->after('title');
            $table->decimal('budget_allocated', 15, 2)->nullable()->after('total_attended');
            $table->decimal('total_expenses', 15, 2)->nullable()->after('budget_allocated');
            $table->text('financial_notes')->nullable()->after('total_expenses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['type', 'budget_allocated', 'total_expenses', 'financial_notes']);
        });
    }
};
