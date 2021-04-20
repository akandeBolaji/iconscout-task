<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesToColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colors', function (Blueprint $table) {
            $table->dropColumn('hsl_value');
            $table->string('hue')->nullable();
            $table->string('saturation')->nullable();
            $table->string('lightness')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colors', function (Blueprint $table) {
            $table->string('hsl_value');
            $table->dropColumn('hue');
            $table->dropColumn('saturation');
            $table->dropColumn('lightness');
        });
    }
}
