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
        Schema::create('manual_soa', function (Blueprint $table) {
            $table->id();
            $table->string('Date_of_transaction', 155)->nullable();
            $table->string('Debit', 155)->nullable();
            $table->string('Credit', 155)->nullable();
            $table->string('Status_field', 255)->nullable();
            $table->string('Balance', 155)->nullable();
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
        Schema::dropIfExists('manual_soa');
    }
};
