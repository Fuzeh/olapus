<?php
/**
 * Create adverts migration
 * 
 * Creates data table for module Advert
 * 
 * @category Migration
 * @subpackage Admin
 * @package Olapus
 * @author Jan Drda <jdrda@outlook.com>
 * @copyright Jan Drda
 * @license https://opensource.org/licenses/MIT MIT
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdverts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advert', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->index();
                        $table->string('caption')->nullable();
			$table->text('text')->nullable();
                        $table->string('link_url')->nullable();
                        $table->string('link_title')->nullable();
                        $table->integer('image_id')->unsigned()->index()->nullable();
                        $table->integer('position')->index();
			$table->timestamps();
                        $table->softDeletes();
		});
                
        Schema::create('advert_advertlocation', function(Blueprint $table)
		{
			$table->integer('advert_id')->unsigned();
                        $table->integer('advertlocation_id')->unsigned();
                        $table->primary(['advert_id', 'advertlocation_id']);
                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('advert');
        Schema::drop('advert_advertlocation');
    }
}
