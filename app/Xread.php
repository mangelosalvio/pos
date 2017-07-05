<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Xread extends Model
{
    use SoftDeletes;
    protected $fillable = [
      'xread_datetime',
      'cashier_id',
      'gross_amount',
      'vat_discount_amount',
      'discount_amount',
      'net_amount',
      'invoice_from',
      'invoice_to'
    ];
    protected $dates = [
      'xread_datetime'
    ];

    public function cashier(){
        return $this->belongsTo('App\User','cashier_id');
    }
}
