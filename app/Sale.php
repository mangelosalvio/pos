<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $dates = ['sale_datetime'];

    protected $fillable = [
      'sale_datetime','table_id', 'cashier_id', 'customer_name', 'customer_address',
      'gross_amount', 'vat_discount_amount', 'discount_amount', 'vat_exempt_amount',
      'vat_sales_amount', 'non_vat_amount', 'net_amount', 'cash_amount', 'change_amount'
    ];

    public function products(){
        return $this->belongsToMany('App\Product','product_sale','sale_id','product_id')
            ->withPivot('quantity','price','gross_amount','vat_discount_amount',
            'discount_amount','vat_exempt_amount','vat_sales_amount','vat_amount',
            'non_vat_amount','net_amount')
            ->withTimestamps();
    }

    public function table(){
        return $this->belongsTo('App\Table');
    }

    public function user(){
        return $this->belongsTo('App\User','cashier_id');
    }


}
