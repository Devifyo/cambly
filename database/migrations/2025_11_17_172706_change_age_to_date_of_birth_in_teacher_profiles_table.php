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
        Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->dropColumn('age');

                // Add the new column
                $table->date('date_of_birth')->nullable()->after('english_level');
                $table->string('country_residence')->nullable()->after('date_of_birth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
             $table->dropColumn('date_of_birth');
            $table->integer('age')->nullable();
            $table->dropColumn('country_residence');
        });
    }
};
