<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xreads', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('xread_datetime');
            $table->bigInteger('cashier_id',false,true);
            $table->decimal('gross_amount',12,2);
            $table->decimal('vat_discount_amount',12,2);
            $table->decimal('discount_amount',12,2);
            $table->decimal('net_amount',12,2);
            $table->integer('invoice_from',false,true);
            $table->integer('invoice_to',false,true);
            $table->softDeletes();
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
        Schema::drop('xreads');
    }
}
