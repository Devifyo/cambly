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
        Schema::table('ticket_ledgers', function (Blueprint $table) {
              $table->enum('credit_by', ['admin', 'stripe'])
                ->default('stripe')
                ->after('hold_credits');

            $table->string('reason', 255)
                ->default('tickets credited by monthly subscription')
                ->after('credit_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
             $table->dropColumn(['credit_by', 'reason']);
        });
    }
};
