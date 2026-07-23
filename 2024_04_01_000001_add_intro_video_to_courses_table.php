<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('intro_video_url', 500)->nullable()->after('image');
            $table->string('intro_video_path')->nullable()->after('intro_video_url');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['intro_video_url', 'intro_video_path']);
        });
    }
};
