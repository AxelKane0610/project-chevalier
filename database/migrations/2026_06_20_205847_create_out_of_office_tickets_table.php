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
        Schema::create('out_of_office_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->enum('type_of_leave', [1, 2, 3, 4, 5, 6]);
            $table->string('reasons_for_leave');
            $table->float('days_of_leave');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', [1, 2, 3, 4])->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('out_of_office_tickets');
    }
};
