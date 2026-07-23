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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_admin_new_student')->default(true)->after('notify_emails');
            $table->boolean('notify_admin_new_download')->default(true)->after('notify_admin_new_student');
            $table->boolean('notify_admin_emails')->default(true)->after('notify_admin_new_download');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_admin_new_student', 'notify_admin_new_download', 'notify_admin_emails']);
        });
    }
};
