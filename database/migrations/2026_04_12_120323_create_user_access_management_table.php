<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_access_management', function (Blueprint $table) {
            $table->id();

            // 🔹 DATA USER
            $table->string('nama_user');
            $table->string('email')->nullable();
            $table->string('role')->nullable();
            $table->string('divisi')->nullable();
            $table->string('cabang')->nullable();
            $table->string('penanggung_jawab')->nullable();

            // 🔹 STATUS USER
            $table->enum('status', ['active', 'inactive', 'resign'])->default('active');
            $table->date('tgl_resign')->nullable();

            // 🔹 CRITICAL ACCESS
            $table->boolean('is_critical')->default(false);

            // 🔹 SYSTEM CATEGORY
            $table->enum('kategori_system', [
                'SYOP',
                'SERVER',
                'ACCURATE',
                'JPAYROLL',
                'HELPDESK',
                'CRS'
            ]);

            // 🔹 MENU & AKSES
            $table->string('menu_akses')->nullable();

            $table->boolean('can_create')->default(false);
            $table->boolean('can_view')->default(true);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_approve')->default(false);

            // 🔹 APPROVAL CEO
            $table->boolean('approval_ceo')->default(false);
            $table->timestamp('approval_at')->nullable();

            // 🔹 WORKFLOW STATUS
            $table->enum('workflow_status', [
                'draft',
                'pending_approval',
                'approved',
                'rejected'
            ])->default('draft');

            // 🔹 DISABLE TRACKING
            $table->string('disabled_by')->nullable();
            $table->timestamp('disabled_at')->nullable();

            // 🔹 AUDIT TRAIL
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            // 🔹 KETERANGAN & LAMPIRAN
            $table->text('keterangan')->nullable();
            $table->string('lampiran')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_access_management');
    }
};