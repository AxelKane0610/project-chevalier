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
        Schema::create('hps_export_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ce_owner')->nullable()->constrained('users');
            $table->string('asset_tag');
            $table->date('export_date');
            $table->enum('export_site', ['1', '2', '3', '4']);
            $table->string('export_location')->nullable();
            $table->string('export_product_number')->nullable();
            $table->string('export_model')->nullable();
            $table->string('export_serial_number')->nullable();
            $table->string('export_box_serial_number')->nullable();
            $table->string('hps_receipt')->nullable();
            $table->enum('program_support', ['1', '2', '3', '4', '5', '6'])->nullable();
            $table->string('re_import_model')->nullable();
            $table->string('re_import_serial_number')->nullable();
            $table->string('re_import_box_serial_number')->nullable();
            $table->string('re_import_product_number')->nullable();
            
            // 1. Sửa lại: thêm ->nullable() vào re_import_site
            $table->enum('re_import_site', ['1', '2', '3', '4'])->nullable(); 

            $table->string('re_import_location')->nullable();
            $table->enum('current_status', ['1', '2', '3', '4', '5'])->nullable();
            $table->string('note')->nullable();
            $table->enum('re_import_status', ['1', '2', '3', '4', '5'])->nullable();
            $table->foreignId('user_export')->constrained('users');
            
            // 2. Sửa lại: đưa ->nullable() lên TRƯỚC ->constrained()
            $table->foreignId('user_re_import')->nullable()->constrained('users'); 

            $table->date('re_import_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hps_export_details');
    }
};
