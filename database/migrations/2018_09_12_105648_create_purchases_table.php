<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('delivery_addr');
            $table->string('contact_email');
            $table->string('consumer_name');
            $table->integer('total_price');
            $table->string('edycard_id');
            $table->integer('liquor1_id');
            $table->integer('liquor2_id');
            $table->integer('liquor3_id');
            $table->integer('liquor4_id');
            $table->integer('liquor5_id');
            $table->integer('set_id');
            $table->string('edy_id');
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
        Schema::dropIfExists('purchases');
    }
}
