<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'subcategory')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('subcategory', 100)->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'subcategory')) {
            Schema::table('assets', fn (Blueprint $table) => $table->dropColumn('subcategory'));
        }
    }
};
