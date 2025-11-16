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
        Schema::table('student_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('student_profiles', 'age')) {
                $table->dropColumn('age');
            }

            // add new date_of_birth field
            if (!Schema::hasColumn('student_profiles', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('preferred_name');
            }
            

            if (!Schema::hasColumn('student_profiles', 'country_residence')) {
                $table->string('country_residence', 100)->nullable()->after('date_of_birth');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('student_profiles', 'date_of_birth')) {
                $table->dropColumn('date_of_birth');
            }
            if (Schema::hasColumn('student_profiles', 'country_residence')) {
                $table->dropColumn('country_residence');
            }
            if (!Schema::hasColumn('student_profiles', 'age')) {
                $table->integer('age')->nullable()->after('preferred_name');
            }
        });
    }
};
