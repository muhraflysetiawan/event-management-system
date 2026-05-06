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
        Schema::table('event_requirements', function (Blueprint $table) {
            $table->boolean('is_required')->default(true)->after('question_text');
        });

        Schema::table('survey_questions', function (Blueprint $table) {
            $table->boolean('is_required')->default(true)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_requirements', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });

        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
    }
};
