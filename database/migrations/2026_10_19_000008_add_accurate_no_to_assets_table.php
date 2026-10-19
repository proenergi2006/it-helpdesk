<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'accurate_no')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('accurate_no', 50)->nullable()->unique();
            });
        }
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropUnique(['accurate_no']);
            $table->dropColumn('accurate_no');
        });
    }
};
