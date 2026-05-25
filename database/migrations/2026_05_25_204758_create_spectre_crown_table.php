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
        Schema::create('spectre_crown_warehouse', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->string('serial_number');
            $table->string('box_serial_number');
            $table->string('product_number');
            $table->string('model');
            $table->enum('category', ['1', '2', '3', '4', '5', '6', '7', '8']);
            $table->enum('asset_type', ['1', '2', '3', '4', '5', '6']);
            $table->integer('quantity');
            $table->enum('warehouse', ['1', '2', '3']);
            $table->enum('available_status', ['1', '2', '3']);
            $table->enum('condition', ['1', '2', '3']);
            $table->string('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spectre_crown_warehouse');
    }
};
