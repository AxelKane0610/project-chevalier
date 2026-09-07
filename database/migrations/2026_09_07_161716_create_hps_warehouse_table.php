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
        Schema::create('hps_warehouse', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->foreignId('user_id')->constrained();
            $table->string('serial_number');
            $table->string('box_serial_number');
            $table->string('product_number');
            $table->string('model');
            $table->date('import_date');
            $table->enum('import_source', ['1', '2', '3']);
            $table->string('po_number');
            $table->enum('po_type', ['1', '2', '3']);
            $table->string('invoice');
            $table->integer('price');
            $table->enum('import_status', ['1', '2']);
            $table->enum('site', ['1', '2', '3', '4']);
            $table->string('location')->nullable();
            $table->string('note')->nullable();
            $table->enum('current_status', ['1', '2', '3', '4', '5']);
            $table->string('current_hps_receipt');
            $table->string('current_serial_number');
            $table->string('current_box_serial_number');
            $table->string('current_product_number');
            $table->enum('current_site', ['1', '2', '3', '4']);
            $table->string('current_location')->nullable();
            $table->enum('unit-re-import-status', ['1', '2', '3', '4', '5']);
            $table->integer('current_export_ticket_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hps_warehouse');
    }
};
