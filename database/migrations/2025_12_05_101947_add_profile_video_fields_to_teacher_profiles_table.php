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
            $table->string('games', 100)->nullable()->after('short_bio');
            $table->text('introduction')->nullable()->after('games');
            $table->string('youtube_url', 255)->nullable()->after('introduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
              $table->dropColumn(['games', 'introduction', 'youtube_url']);
        });
    }
};
