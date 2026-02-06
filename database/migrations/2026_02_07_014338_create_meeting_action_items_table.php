<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_action_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->string('task'); // action item
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->enum('status', ['todo','in_progress','done'])->default('todo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_action_items');
    }
};
