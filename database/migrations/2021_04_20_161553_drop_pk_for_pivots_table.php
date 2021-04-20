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
           $table->dropColumn('created_at');
           $table->dropColumn('updated_at');
        });
        Schema::table('color_icon', function($table) {
           $table->dropColumn('id');
           $table->dropColumn('created_at');
           $table->dropColumn('updated_at');
        });
        Schema::table('category_icon', function($table) {
           $table->dropColumn('id');
           $table->dropColumn('created_at');
           $table->dropColumn('updated_at');
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
           $table->timestamps();
        });
        Schema::table('color_icon', function($table) {
           $table->bigInteger('id');
           $table->timestamps();
        });
        Schema::table('category_icon', function($table) {
           $table->bigInteger('id');
           $table->timestamps();
        });
    }
}
