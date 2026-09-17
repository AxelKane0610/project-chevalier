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
        Schema::create('bbnt_partner_onsite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('submit_date')->nullable();
            $table->string('requester_name');
            $table->string('partner_address');
            $table->string('partner_city');
            $table->enum('onsite_type', ['1', '2']);
            $table->string('document_name');
            $table->integer('total_case');
            $table->float('total_amount');
            $table->enum('status', ['1', '2', '3']);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbnt_partner_onsite');
    }
};
