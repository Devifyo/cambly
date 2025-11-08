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
        Schema::table('credit_transactions', function (Blueprint $table) {
           // Add ticket ledger foreign key
            $table->unsignedBigInteger('ticket_ledger_id')
                  ->nullable()
                  ->after('id');

            // Add polymorphic relation fields
            $table->unsignedBigInteger('action_id')
                  ->nullable()
                  ->after('ticket_ledger_id');
            $table->string('action_type')
                  ->nullable()
                  ->after('action_id');

            // Add foreign key constraint
            $table->foreign('ticket_ledger_id')
                  ->references('id')
                  ->on('ticket_ledgers')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
               $table->dropForeign(['ticket_ledger_id']);
            $table->dropColumn(['ticket_ledger_id', 'action_id', 'action_type']);
        });
    }
};
