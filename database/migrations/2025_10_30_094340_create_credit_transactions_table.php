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
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('cycle_number');
            $table->integer('credits'); // Positive for issued, negative for used
            $table->enum('type', ['issued', 'used', 'hold', 'release'])->default('issued');
            $table->string('reason')->nullable(); // e.g., 'subscription', 'invoice_paid', 'checkout_payment'
            $table->string('reference')->nullable()->unique(); // Stripe invoice_id, payment_intent_id, etc.
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'cycle_number']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
