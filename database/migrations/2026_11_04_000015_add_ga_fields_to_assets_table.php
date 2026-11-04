<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom tambahan untuk aset GA / Logistik (mengikuti format Master Asset)
        $columns = [
            'asset_class'        => fn (Blueprint $t) => $t->string('asset_class', 20)->nullable(),
            'old_asset_no'       => fn (Blueprint $t) => $t->string('old_asset_no', 60)->nullable()->index(),
            'qty'                => fn (Blueprint $t) => $t->unsignedInteger('qty')->default(1),
            'acquisition_amount' => fn (Blueprint $t) => $t->decimal('acquisition_amount', 18, 2)->nullable(),
            'condition_note'     => fn (Blueprint $t) => $t->text('condition_note')->nullable(),
            'vat_status'         => fn (Blueprint $t) => $t->string('vat_status', 50)->nullable(),
            'disposed_at'        => fn (Blueprint $t) => $t->date('disposed_at')->nullable(),
            'disposal_reason'    => fn (Blueprint $t) => $t->string('disposal_reason', 255)->nullable(),
            'disposal_ba_no'     => fn (Blueprint $t) => $t->string('disposal_ba_no', 100)->nullable(),
            'disposal_amount'    => fn (Blueprint $t) => $t->decimal('disposal_amount', 18, 2)->nullable(),
        ];

        foreach ($columns as $name => $define) {
            if (!Schema::hasColumn('assets', $name)) {
                Schema::table('assets', fn (Blueprint $t) => $define($t));
            }
        }
    }

    public function down(): void
    {
        $drop = array_filter(
            ['asset_class', 'old_asset_no', 'qty', 'acquisition_amount', 'condition_note', 'vat_status', 'disposed_at', 'disposal_reason', 'disposal_ba_no', 'disposal_amount'],
            fn ($c) => Schema::hasColumn('assets', $c)
        );
        if ($drop) {
            Schema::table('assets', fn (Blueprint $t) => $t->dropColumn(array_values($drop)));
        }
    }
};
