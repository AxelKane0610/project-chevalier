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
        //
        Schema::create('attachments_table', function (Blueprint $table) {
            $table->id(); // Tự động tạo id Primary Key, Auto Increment
            $table->string('type_of_ticket');
            $table->unsignedBigInteger('ticket_id');
            $table->string('file_path');
            $table->string('name');
            $table->timestamps(); // Tự động tạo created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
