<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'department')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('department', 20)->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['department']);
            $table->dropColumn('department');
        });
    }
};
