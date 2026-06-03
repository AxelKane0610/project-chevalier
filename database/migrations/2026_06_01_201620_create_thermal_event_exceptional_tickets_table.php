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
        Schema::create('thermal_event_exceptional_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('ticket_receipt');
            $table->enum('status', ['1', '2', '3', '4','5'])->default('1');
            $table->string('serial_number');
            $table->string('product_number');
            $table->string('product_model');
            $table->string('description');
            $table->string('cdax_id');
            $table->enum('customer_type', ['1', '2', '3']);
            $table->string('company_customer_name');
            $table->string('part_mo_number');
            $table->string('part_number');
            $table->string('part_description');
            $table->string('part_ct_number');
            $table->string('user_observations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thermal_event_exceptional_tickets');
    }
};
