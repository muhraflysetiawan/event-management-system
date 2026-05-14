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
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('head_id')->nullable()->after('lecturer_id')->constrained('users')->nullOnDelete();
            $table->text('head_signature')->nullable()->after('head_id'); // Base64
            $table->foreignId('acoo_id')->nullable()->after('head_signature')->constrained('users')->nullOnDelete();
            $table->text('acoo_signature')->nullable()->after('acoo_id'); // Base64
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['head_id']);
            $table->dropForeign(['acoo_id']);
            $table->dropColumn(['head_id', 'head_signature', 'acoo_id', 'acoo_signature']);
        });
    }
};
