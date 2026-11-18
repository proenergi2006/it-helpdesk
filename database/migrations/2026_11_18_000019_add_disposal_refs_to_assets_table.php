<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['disposal_request_no', 'disposal_ref_no'] as $col) {
            if (!Schema::hasColumn('assets', $col)) {
                Schema::table('assets', fn (Blueprint $t) => $t->string($col, 100)->nullable());
            }
        }
    }

    public function down(): void
    {
        foreach (['disposal_request_no', 'disposal_ref_no'] as $col) {
            if (Schema::hasColumn('assets', $col)) {
                Schema::table('assets', fn (Blueprint $t) => $t->dropColumn($col));
            }
        }
    }
};
