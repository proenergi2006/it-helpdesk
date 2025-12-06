<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom cancel_note ke tabel tickets
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->text('cancel_note')->nullable()->after('resolution_note');
        });
    }

    /**
     * Rollback kolom cancel_note
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('cancel_note');
        });
    }
};
