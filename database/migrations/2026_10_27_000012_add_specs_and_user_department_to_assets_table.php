<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = ['spec_cpu', 'spec_ram', 'spec_storage', 'spec_os', 'spec_other', 'user_department'];

    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            foreach ($this->columns as $col) {
                if (!Schema::hasColumn('assets', $col)) {
                    $col === 'spec_other' ? $table->text($col)->nullable() : $table->string($col, 150)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn($this->columns);
        });
    }
};
