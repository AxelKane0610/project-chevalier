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
        Schema::create('ttex_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('ttex_bill')->nullable();
            $table->enum('category',[1, 2, 3, 4, 5, 6]);
            $table->enum('shipment_type', [1, 2, 3]);
            $table->enum('part_status', [1, 2, 3]);
            $table->enum('status', [1, 2, 3])->default(1);
            $table->date('part_return_deadline')->nullable();
            $table->string('sender_info');
            $table->string('receiver_info');
            $table->string('shipment_description');
            $table->string('note')->nullable();
            $table->date('booking_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ttex_tickets');
    }
};
