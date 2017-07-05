<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->timestamp('sale_datetime');
            $table->bigInteger('table_id');
            $table->bigInteger('cashier_id');
            $table->string('customer_name');
            $table->string('customer_address');
            $table->decimal('gross_amount',12,2);

            $table->decimal('vat_discount_amount',12,2);
            $table->decimal('discount_amount',12,2);
            $table->decimal('vat_exempt_amount',12,2);
            $table->decimal('vat_sales_amount',12,2);
            $table->decimal('vat_amount',12,2);
            $table->decimal('non_vat_amount',12,2);
            $table->decimal('net_amount',12,2);


            $table->decimal('cash_amount',12,2);
            $table->decimal('change_amount',12,2);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_sale', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('product_id');
            $table->bigInteger('sale_id');

            $table->decimal('quantity',12,2);
            $table->decimal('price',12,2);
            $table->decimal('gross_amount',12,2);

            $table->decimal('vat_discount_amount',12,2);
            $table->decimal('discount_amount',12,2);
            $table->decimal('vat_exempt_amount',12,2);
            $table->decimal('vat_sales_amount',12,2);
            $table->decimal('vat_amount',12,2);
            $table->decimal('non_vat_amount',12,2);
            $table->decimal('net_amount',12,2);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sales');
        Schema::drop('product_sale');
    }
}
