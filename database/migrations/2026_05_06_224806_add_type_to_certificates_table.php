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
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'type')) {
                $table->string('type')->default('participation')->after('event_id');
            }
            
            // Create separate index for user_id if it doesn't exist, 
            // as it's required for the foreign key and might be using the unique index.
            $table->index('user_id', 'certificates_user_id_index');
            
            // Now we can safely drop the unique index
            $table->dropUnique(['user_id', 'event_id']);
            
            $table->unique(['user_id', 'event_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'event_id', 'type']);
            
            $table->unique(['user_id', 'event_id']);
            
            $table->dropIndex('certificates_user_id_index');
            
            if (Schema::hasColumn('certificates', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
