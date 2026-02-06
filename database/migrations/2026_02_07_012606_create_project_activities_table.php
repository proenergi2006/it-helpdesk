<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('event', 50); // status_changed, assignees_changed, dates_changed, updated
            $table->string('title');     // ringkas untuk ditampilkan
            $table->json('meta')->nullable(); // data detail: before/after dll

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_activities');
    }
};
