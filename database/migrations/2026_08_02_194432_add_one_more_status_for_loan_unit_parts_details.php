<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
/**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        ALTER TABLE loan_unit_ticket_parts_details
        MODIFY COLUMN status ENUM(
            '1',
            '2',
            '3',
            '4',
            '5'
        ) NOT NULL
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
        ALTER TABLE loan_unit_ticket_parts_details
        MODIFY COLUMN status ENUM(
            '1',
            '2',
            '3',
            '4',
            '5'
        ) NOT NULL
    ");
    }
};
