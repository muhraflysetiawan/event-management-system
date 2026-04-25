<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->string('student_id')->nullable()->after('email');
            $table->string('phone')->nullable()->after('student_id');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('department')->nullable()->after('avatar');
            $table->string('organization')->nullable()->after('department');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'student_id', 'phone', 'avatar', 'department', 'organization']);
        });
    }
};
