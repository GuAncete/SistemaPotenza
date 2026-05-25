<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operarios', function (Blueprint $table) {
            $table->string('cargo')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('operarios', function (Blueprint $table) {
            $table->string('cargo')->nullable(false)->default('')->change();
        });
    }
};
