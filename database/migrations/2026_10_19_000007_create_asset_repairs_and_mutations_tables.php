<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_repairs')) {
            Schema::create('asset_repairs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('ticket_id')->nullable()->index();
                $table->date('repaired_at');
                $table->text('problem');
                $table->text('action')->nullable();
                $table->string('vendor', 150)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('asset_repair_parts')) {
            Schema::create('asset_repair_parts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_repair_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('detail')->nullable();
                $table->unsignedInteger('qty')->default(1);
                $table->decimal('cost', 14, 2)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('asset_mutations')) {
            Schema::create('asset_mutations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->date('mutated_at');
                $table->string('from_cabang', 50)->nullable();
                $table->string('from_location', 150)->nullable();
                $table->string('from_user', 150)->nullable();
                $table->string('to_cabang', 50)->nullable();
                $table->string('to_location', 150)->nullable();
                $table->string('to_user', 150)->nullable();
                $table->string('reason', 500)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_mutations');
        Schema::dropIfExists('asset_repair_parts');
        Schema::dropIfExists('asset_repairs');
    }
};
