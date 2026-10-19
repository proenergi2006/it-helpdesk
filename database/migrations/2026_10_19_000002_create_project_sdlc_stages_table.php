<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('projects', 'ticket_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->unsignedBigInteger('ticket_id')->nullable()->index();
            });
        }

        if (!Schema::hasTable('project_sdlc_stages')) {
            Schema::create('project_sdlc_stages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->unsignedSmallInteger('position')->default(0);
                $table->timestamp('done_at')->nullable();
                $table->unsignedBigInteger('done_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_sdlc_stages');
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('ticket_id');
        });
    }
};
