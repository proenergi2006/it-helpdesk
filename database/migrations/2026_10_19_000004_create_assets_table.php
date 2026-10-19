<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->string('code', 30)->nullable()->unique();
                $table->string('name');
                $table->string('type', 50);
                $table->string('brand', 100)->nullable();
                $table->string('model', 100)->nullable();
                $table->string('serial_number', 100)->nullable()->index();
                $table->string('cabang', 50)->nullable()->index();
                $table->string('location', 150)->nullable();
                $table->string('user_name', 150)->nullable();
                $table->string('status', 20)->default('aktif')->index();
                $table->date('purchase_date')->nullable();
                $table->date('warranty_until')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('tickets', 'asset_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedBigInteger('asset_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('asset_id');
        });
        Schema::dropIfExists('assets');
    }
};
