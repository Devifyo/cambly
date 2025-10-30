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
            // nullable so existing rows are fine
            $table->string('stripe_subscription_id')->nullable()->after('student_id');
            $table->string('stripe_invoice_id')->nullable()->after('stripe_subscription_id');

            // unique index to prevent duplicate invoice rows per student (or per subscription)
            // choose index columns that fit your uniqueness needs; this uses student+invoice
            $table->unique(['student_id', 'stripe_invoice_id'], 'uniq_student_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_ledgers', function (Blueprint $table) {
             $table->dropUnique('uniq_student_invoice');
            $table->dropColumn(['stripe_subscription_id', 'stripe_invoice_id']);
        });
    }
};
