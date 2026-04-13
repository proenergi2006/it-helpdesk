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
        Schema::create('user_access_menus', function (Blueprint $table) {
            $table->id();

            // 🔹 RELASI KE HEADER
            $table->unsignedBigInteger('user_access_id');

            // 🔹 MENU
            $table->string('menu_name');

            // 🔹 HAK AKSES
            $table->boolean('can_create')->default(false);
            $table->boolean('can_view')->default(true);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_approve')->default(false);

            // 🔹 OPTIONAL (kalau mau grouping)
            $table->string('module')->nullable(); // contoh: master, transaksi, laporan

            $table->timestamps();

            // 🔥 FOREIGN KEY
            $table->foreign('user_access_id')
                  ->references('id')
                  ->on('user_access_management')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_access_menus');
    }
};