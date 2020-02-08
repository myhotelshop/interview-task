<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversionTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('conversions', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedBigInteger('customer_id');
      $table->string('platform');
      $table->unsignedInteger('revenue');
      $table->string('booking_number',50);
      $table->date('date_of_contact');
      $table->timestamps();
      $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('conversions');
  }
}
