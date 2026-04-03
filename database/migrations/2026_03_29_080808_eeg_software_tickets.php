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
        Schema::create('eeg_software_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('eeg_software_tickets_reciept'); //->unique()
            $table->enum('support_type', [1, 2, 3, 4]);
            $table->enum('priority', [1, 2, 3, 4]);
            $table->longText('description');
            $table->enum('status', [1, 2, 3, 4, 5])->default(1);
            $table->timestamps();

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
