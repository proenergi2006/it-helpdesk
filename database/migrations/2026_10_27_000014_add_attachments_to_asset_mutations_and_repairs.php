<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = ['photo_path', 'receipt_path', 'from_user_department', 'to_user_department'];

    public function up(): void
    {
        Schema::table('asset_mutations', function (Blueprint $table) {
            foreach ($this->columns as $col) {
                if (!Schema::hasColumn('asset_mutations', $col)) {
                    $table->string($col)->nullable();
                }
            }
        });

        if (!Schema::hasTable('asset_repair_photos')) {
            Schema::create('asset_repair_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_repair_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->string('original_name');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_repair_photos');
        Schema::table('asset_mutations', function (Blueprint $table) {
            $table->dropColumn($this->columns);
        });
    }
};
