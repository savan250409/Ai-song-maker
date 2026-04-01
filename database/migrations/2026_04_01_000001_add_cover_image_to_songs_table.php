<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('songs', 'cover_image')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->string('cover_image')->nullable()->after('song_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('songs', 'cover_image')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->dropColumn('cover_image');
            });
        }
    }
};
