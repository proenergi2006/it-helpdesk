<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 // Judul meeting
            $table->string('project_name')->nullable(); // Opsional: project terkait
            $table->dateTime('meeting_at');          // Tanggal & waktu meeting
            $table->string('location')->nullable();  // lokasi/online link
            $table->longText('notes')->nullable();   // MOM rich text (Trix)
            $table->string('attachment')->nullable(); // optional file path (kalau nanti mau)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
