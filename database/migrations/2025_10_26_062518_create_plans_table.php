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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('interval', ['one_time', 'monthly', 'yearly'])->default('monthly'); //  added one_time
            $table->integer('credits_per_cycle');
            $table->decimal('price', 8, 2);
            $table->text('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable(); 
            $table->boolean('is_popular')->default(false); //  new field
            $table->string('icon_path')->nullable(); //  new field
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
