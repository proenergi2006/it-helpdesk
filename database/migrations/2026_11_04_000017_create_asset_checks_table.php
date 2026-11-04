<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_checks')) {
            Schema::create('asset_checks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id')->index();
                $table->string('condition', 20);
                $table->string('cabang', 50)->nullable();
                $table->string('room', 150)->nullable();
                $table->boolean('location_mismatch')->default(false);
                $table->text('note')->nullable();
                $table->unsignedBigInteger('checked_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_checks');
    }
};
