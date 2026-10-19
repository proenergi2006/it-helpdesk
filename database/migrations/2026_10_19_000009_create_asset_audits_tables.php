<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_audits')) {
            Schema::create('asset_audits', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('cabang', 50)->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('asset_audit_items')) {
            Schema::create('asset_audit_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_audit_id')->constrained()->cascadeOnDelete();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('scanned_by')->nullable();
                $table->timestamp('scanned_at');
                $table->string('note')->nullable();
                $table->unique(['asset_audit_id', 'asset_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audit_items');
        Schema::dropIfExists('asset_audits');
    }
};
