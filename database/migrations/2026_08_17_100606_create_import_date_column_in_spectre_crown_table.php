<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Livewire\after;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spectre_crown_warehouse', function (Blueprint $table) {
            $table->date('import_date')->nullable()->after('warehouse');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spectre_crown_warehouse', function (Blueprint $table) {
            //
            $table->dropColumn('import_date');
        });
    }
};
