<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('method', 10)->nullable()->after('action');
            $table->string('route', 255)->nullable()->after('method');
            $table->json('payload')->nullable()->after('description');
            $table->unsignedSmallInteger('status_code')->nullable()->after('payload');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['method', 'route', 'payload', 'status_code']);
        });
    }
};
