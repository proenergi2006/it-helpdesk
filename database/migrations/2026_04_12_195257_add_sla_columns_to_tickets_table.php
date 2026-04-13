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
        Schema::table('tickets', function (Blueprint $table) {
            // waktu mulai dikerjakan oleh IT
            $table->timestamp('taken_at')->nullable()->after('taken_by');

            // waktu ticket selesai / resolved
            $table->timestamp('resolved_at')->nullable()->after('taken_at');

            // target SLA response dalam menit
            $table->integer('sla_response_minutes')->nullable()->after('resolved_at');

            // target SLA resolution dalam menit
            $table->integer('sla_resolution_minutes')->nullable()->after('sla_response_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'taken_at',
                'resolved_at',
                'sla_response_minutes',
                'sla_resolution_minutes',
            ]);
        });
    }
};