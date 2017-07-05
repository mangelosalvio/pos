<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zread extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'zread_datetime',
        'cashier_id',
        'gross_amount',
        'vat_discount_amount',
        'discount_amount',
        'net_amount',
        'invoice_from',
        'invoice_to'
    ];
    protected $dates = [
        'zread_datetime'
    ];

    public function cashier(){
        return $this->belongsTo('App\User','cashier_id');
    }
}
