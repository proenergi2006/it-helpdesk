<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('project_name'); // nama project
            $table->string('title');        // nama dokumen/judul
            $table->string('type');         // CR/BRD/DEV/UAT/IMP
            $table->text('notes')->nullable();

            // file metadata
            $table->string('original_name');
            $table->string('path');         // storage path
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            // audit
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['project_name', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
