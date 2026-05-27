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
        Schema::create('laser_engraving_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('receipt');
            $table->enum('status', ['1', '2', '3', '4'])->default('1');
            $table->enum('priority', ['1', '2', '3']);
            $table->string('info_base');
            $table->string('barcode_info')->nullable()->change(); // Thêm trường barcode_info, cho phép null và có thể thay đổi kiểu dữ liệu nếu cần
            $table->longText('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laser_engraving_tickets');
    }
};
