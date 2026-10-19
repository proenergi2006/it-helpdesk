<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('project_sdlc_stages', 'document_path')) {
            Schema::table('project_sdlc_stages', function (Blueprint $table) {
                $table->string('document_path')->nullable();
                $table->string('document_name')->nullable();
                $table->unsignedBigInteger('document_uploaded_by')->nullable();
                $table->timestamp('document_uploaded_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('project_sdlc_stages', function (Blueprint $table) {
            $table->dropColumn(['document_path', 'document_name', 'document_uploaded_by', 'document_uploaded_at']);
        });
    }
};
