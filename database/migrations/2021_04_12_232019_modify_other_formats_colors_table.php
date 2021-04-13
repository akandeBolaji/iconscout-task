<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOtherFormatsColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colors', function(Blueprint $table) {
            $table->renameColumn('value', 'hex_value');
            $table->string('hsl_value')->nullable();
            $table->string('dec_value')->nullable();
            $table->string('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colors', function(Blueprint $table) {
            $table->renameColumn('hex_value', 'value');
            $table->dropColumn('hsl_value');
            $table->dropColumn('dec_value');
            $table->dropColumn('name'); 
        });
       
    }
}
