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
        Schema::table('webhook_events', function (Blueprint $table) {
              $table->foreignId('user_id')
                ->nullable()
                ->after('id') // Adjust as needed (can be after 'type' etc.)
                ->constrained('users') // Creates a foreign key reference to users.id
                ->nullOnDelete(); // Set to null if the user is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
             $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
