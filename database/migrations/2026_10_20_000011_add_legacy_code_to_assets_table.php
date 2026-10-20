<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'legacy_code')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('legacy_code', 30)->nullable()->unique();
            });
        }
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropUnique(['legacy_code']);
            $table->dropColumn('legacy_code');
        });
    }
};
