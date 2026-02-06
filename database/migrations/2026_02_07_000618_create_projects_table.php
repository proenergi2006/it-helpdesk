<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name');              // nama project
            $table->text('description')->nullable();

            $table->string('status')->default('backlog'); // backlog|todo|in_progress|review|done

            $table->date('start_date')->nullable(); // mulai kapan
            $table->date('due_date')->nullable();   // selesai kapan (target)
            $table->date('done_date')->nullable();  // selesai real (opsional, kalau status done)

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
