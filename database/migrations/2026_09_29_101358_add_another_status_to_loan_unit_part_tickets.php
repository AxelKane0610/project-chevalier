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
        Schema::table('loan_unit_part_tickets', function (Blueprint $table) {
            //
            $table->enum('status', ['1', '2', '3', '4', '5'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_unit_part_tickets', function (Blueprint $table) {
            //
            $table->enum('status', ['1', '2', '3', '4'])->change();

        });
    }
};
