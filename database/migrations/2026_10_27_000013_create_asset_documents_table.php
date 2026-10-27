<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_documents')) {
            Schema::create('asset_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->string('type', 20); // box, serial, receipt
                $table->string('path');
                $table->string('original_name');
                $table->string('mime', 100)->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();
                $table->unique(['asset_id', 'type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_documents');
    }
};
