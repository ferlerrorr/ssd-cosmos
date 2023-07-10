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
        Schema::create('esoa_rbank_ssdi', function (Blueprint $table) {
            $table->id();
            $table->string('Statement_period', 155)->nullable();
            $table->string('Client_name', 155)->nullable();
            $table->string('Client_description', 255)->nullable();
            $table->string('Account_number', 155)->nullable();
            $table->string('Transaction_date', 155)->nullable();
            $table->string('Transaction_type', 255)->nullable();
            $table->string('Store_code', 155)->nullable();
            $table->string('Check_number', 155)->nullable();
            $table->string('Withdrawal', 155)->nullable();
            $table->string('Deposit', 155)->nullable();
            $table->string('Remarks', 255)->nullable();
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
        Schema::dropIfExists('esoa_rbank_ssdi');
    }
};
