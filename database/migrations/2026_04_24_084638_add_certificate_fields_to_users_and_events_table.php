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
        Schema::table('users', function (Blueprint $table) {
            $table->string('signature')->nullable()->after('avatar');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('certificate_template')->nullable();
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('organizer_signature')->nullable(); // Base64 string from signature pad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('signature');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lecturer_id');
            $table->dropColumn(['certificate_template', 'organizer_signature']);
        });
    }
};
