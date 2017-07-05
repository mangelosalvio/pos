<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
      'product_desc', 'price', 'serving_size','stock_code','category_id'
    ];

    public function sales(){
        return $this->belongsToMany('App\Sale','product_sale','product_id','sale_id')
            ->withPivot('quantity','price','gross_amount','vat_discount_amount',
                'discount_amount','vat_exempt_amount','vat_sales_amount','vat_amount',
                'non_vat_amount','net_amount')
            ->withTimestamps();
    }

    public function subItems(){
        return $this->belongsToMany('App\Product','items','product_id','item_id')
            ->withTimestamps();
    }

    public function theSubItems(){
        return $this->belongsToMany('App\Product','items','item_id','product_id')
            ->withTimestamps();
    }

    public function category(){
        return $this->belongsTo('App\Category');
    }





}
