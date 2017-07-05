<?php

namespace App\Http\Controllers;

use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function saleInventory($from_date, $to_date){
        $data =  DB::table('sales')
            ->join('product_sale','sales.id','=','product_sale.sale_id')
            ->join('products','products.id','=','product_sale.product_id')
            ->whereBetween(DB::raw('date(sale_datetime)'),[$from_date,$to_date])
            ->groupBy('product_id')
            ->select(DB::raw('products.product_desc'),DB::raw('SUM(product_sale.quantity) quantity'))
            ->orderBy('products.product_desc')
            ->get();

        /**
         * get summary
         */
        $summary = [];
        $summary['total_quantity'] = 0;
        if ( count($data) ) {
            foreach ( $data as $item ) {
                $summary['total_quantity'] += $item->quantity;
            }
        }

        $from_date = new Carbon($from_date);
        $to_date = new Carbon($to_date);

        return view('reports/sale_inventory',compact(['data','from_date','to_date','summary']));
    }

    public function dailySales($from_date,$to_date){
        $data = DB::table('sales')
            ->join('product_sale','sales.id','=','product_sale.sale_id')
            ->join('products','products.id','=','product_sale.product_id')
            ->whereBetween(DB::raw('date(sale_datetime)'),[$from_date,$to_date])
            ->groupBy(DB::raw('date(sale_datetime)'))
            ->select(DB::raw('date(sale_datetime) date'),DB::raw('SUM(product_sale.net_amount) amount'))
            ->orderBy('date')
            ->get();

        /**
         * get summary
         */
        $summary = [];
        $summary['total_amount'] = 0;
        if ( count($data) ) {
            foreach ( $data as $item ) {
                $summary['total_amount'] += $item->amount;
            }
        }

        $from_date = new Carbon($from_date);
        $to_date = new Carbon($to_date);

        return view('reports/daily_sales',compact(['data','from_date','to_date','summary']));
    }

    public function salesTransaction($from_date, $to_date){

        $Sales = Sale::whereBetween(DB::raw('date(sale_datetime)'),[$from_date,$to_date])
            ->orderBy('id')
            ->get();

        $from_date = new Carbon($from_date);
        $to_date = new Carbon($to_date);

        return view('reports/sales_transaction',compact(['Sales','from_date','to_date']));
    }

    public function voidedSales($from_date, $to_date){

        $Sales = Sale::onlyTrashed()
            ->whereBetween(DB::raw('date(sale_datetime)'),[$from_date,$to_date])
            ->orderBy('id')
            ->get();

        $from_date = new Carbon($from_date);
        $to_date = new Carbon($to_date);

        return view('reports/voided_sales',compact(['Sales','from_date','to_date']));
    }

    public function invoice($id){
        $Sale = Sale::find($id);
        //dd($Sale->table);
        return view('reports/sales_invoice',compact(['Sale']));
    }
}
