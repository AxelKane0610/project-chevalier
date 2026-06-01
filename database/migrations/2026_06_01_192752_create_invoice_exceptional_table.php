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
        Schema::create('invoice_exceptional_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('ticket_receipt');
            $table->enum('status', ['1', '2', '3', '4'])->default('1');
            $table->string('invoice_number');
            $table->string('serial_number');
            $table->string('product_number');
            $table->date('expired_date');
            $table->date('invoice_date');
            $table->string('product_model');
            $table->string('description');
            $table->string('retail_name');
            $table->string('company_customer_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_exceptional_tickets');
    }
};
