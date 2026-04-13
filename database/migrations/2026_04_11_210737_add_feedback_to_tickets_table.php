<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->after('status');
            $table->text('feedback_comment')->nullable()->after('rating');
            $table->timestamp('feedback_at')->nullable()->after('feedback_comment');
            $table->boolean('is_confirmed_resolved')->nullable()->after('feedback_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'rating',
                'feedback_comment',
                'feedback_at',
                'is_confirmed_resolved',
            ]);
        });
    }
};