<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToLiquorStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liquor_stores', function($table) {
            $table->string('image_url');
            $table->integer('degree');
        });  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('liquor_stores', function($table) {
            $table->dropColumn('image_url');
            $table->dropColumn('degree');
        }); 
    }
}
