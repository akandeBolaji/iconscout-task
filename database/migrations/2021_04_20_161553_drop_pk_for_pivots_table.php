<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPkForPivotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('icon_tag', function($table) {
           $table->dropColumn('id');
        });
        Schema::table('color_icon', function($table) {
           $table->dropColumn('id');
        });
        Schema::table('category_icon', function($table) {
           $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('icon_tag', function($table) {
           $table->bigInteger('id');
        });
        Schema::table('color_icon', function($table) {
           $table->bigInteger('id');
        });
        Schema::table('category_icon', function($table) {
           $table->bigInteger('id');
        });
    }
}
