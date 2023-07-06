<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esoa_bdo_ssdi', function (Blueprint $table) {
            $table->id();
            $table->string('Transaction_date', 155)->nullable();
            $table->string('Requested_datetime', 155)->nullable();
            $table->string('Printed_by', 155)->nullable();
            $table->string('Currency', 155)->nullable();
            $table->string('Account_no', 255)->nullable();
            $table->string('Account_name', 255)->nullable();
            $table->string('Branch', 155)->nullable();
            $table->string('Amount', 255)->nullable();
            $table->string('Transaction_description', 255)->nullable();
            $table->string('Deposit_reference', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esoa_bdo_ssdi');
    }
};
