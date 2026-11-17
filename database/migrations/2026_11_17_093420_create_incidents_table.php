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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hpi_caseid')->nullable();
            $table->string('statuscode')->nullable();
            $table->dateTime('createon')->nullable();
            $table->dateTime('hpi_casecloseddate')->nullable();
            $table->string('hpi_casetypeid')->nullable();
            $table->string('hpi_hpisegment')->nullable();
            $table->string('hpi_pgubusinesssegment')->nullable();
            $table->string('customerid')->nullable();
            $table->string('hpi_productlineid')->nullable();
            $table->string('hpi_productlinecdoid')->nullable();
            $table->string('hpi_product')->nullable();
            $table->string('hpi_serialnumber')->nullable();
            $table->string('hpi_assetid')->nullable();
            $table->string('hpi_productname')->nullable();
            $table->string('hpi_primaryphone')->nullable();
            $table->string('hpi_phonenumber')->nullable();
            $table->string('hpi_caseentitlementid')->nullable();
            $table->string('hpi_entitlementstatus')->nullable();
            $table->date('hpi_startdate')->nullable();
            $table->date('hpi_enddate')->nullable();
            $table->integer('hpi_daysleft')->nullable();
            $table->string('hpi_otccodeid')->nullable();
            $table->longText('hpi_problemdescription')->nullable();
            $table->longText('hpi_noteshistory')->nullable();
            $table->string('hpi_caseresolutioncode')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
