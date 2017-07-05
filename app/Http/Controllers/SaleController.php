<?php

namespace App\Http\Controllers;

use App\Product;
use App\Sale;
use App\User;
use App\Xread;
use App\Zread;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SaleController extends Controller
{
    public function __construct(){
        $this->size = 40;

        $this->label_size = 10;
        $this->quantity_size = 5;
        $this->amount_size = 10;
    }

    private function printFile($filename, $print_location = null ){
    
        if ( $print_location == 1 ) {
            exec("python /etc/pos/tcp_client.py ".Config::get('constants.PRINTER_IP_KITCHEN')." ".Config::get('constants.PRINTER_PORT')." $filename");
        } else {
            exec("python /etc/pos/tcp_client.py ".Config::get('constants.PRINTER_IP_MAIN')." ".Config::get('constants.PRINTER_PORT')." $filename");
        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if ( count($data['orders']) ) {

            if ( !isset( $data['customer_name'] ) ) $data['customer_name'] = '';
            if ( is_numeric($data['_id']) ) {
                /**
                 * for dine-in
                 */
                $Sale = Sale::create([
                    'sale_datetime' => Carbon::now()->toDateTimeString(),
                    'cash_amount' => $data['cash_amount'],
                    'change_amount' => $data['change_amount'],
                    'table_id' => $data['_id'],
                    'cashier_id' => $data['user']['id'],
                    'customer_name' => $data['customer_name']
                ]);
            } else {
                /**
                 * for take-out
                 */
                $Sale = Sale::create([
                    'sale_datetime' => Carbon::now()->toDateTimeString(),
                    'cash_amount' => $data['cash_amount'],
                    'change_amount' => $data['change_amount'],
                    'cashier_id' => $data['user']['id'],
                    'customer_name' => $data['customer_name']
                ]);
            }


            foreach ( $data['orders'] as $order ) {
                if ( count($order['details']) ) {
                    foreach ( $order['details'] as $item ) {
                        $Sale->products()
                            ->attach($item['id'],[
                                'quantity' => $item['quantity'],
                                'price' => $item['price'],
                                'gross_amount' => $item['quantity'] * $item['price'],
                                'vat_discount_amount' => $item['vat_disc'],
                                'discount_amount' => $item['disc'],
                                'vat_exempt_amount' => $item['vat_exempt'],
                                'vat_sales_amount' => round($item['price'] / 1.12, 2),
                                'vat_amount' => round($item['price'] / 1.12 * .12, 2),
                                'non_vat_amount' => 0,
                                'net_amount' => $item['amount']
                            ]);
                    }
                }
            }

            /**
             * Summary
             */

            $Sale->gross_amount        = $Sale->products()->sum('gross_amount');
            $Sale->vat_discount_amount = $Sale->products()->sum('vat_discount_amount');
            $Sale->discount_amount     = $Sale->products()->sum('discount_amount');
            $Sale->vat_exempt_amount   = $Sale->products()->sum('vat_exempt_amount');
            $Sale->vat_sales_amount    = $Sale->products()->sum('vat_sales_amount');
            $Sale->vat_amount          = $Sale->products()->sum('vat_amount');
            $Sale->non_vat_amount      = $Sale->products()->sum('non_vat_amount');
            $Sale->net_amount          = $Sale->products()->sum('net_amount');
            $Sale->save();

            $this->printReceipt($Sale);
        }

        return $request->all();
    }

    public function reprintInvoice(){

        $Sale = Sale::orderBy('id','desc')->first();
        if ( $Sale != null ) $this->printReceipt($Sale);

        return $Sale;
    }

    public function voidSale(Request $request){
        $User = User::whereUsername($request->input('username'))
            ->first();

        if ( $User == null ) return null;
        if ( !$User->hasRole('admin') ) return null;

        if ( Hash::check($request->input('password'), $User->password) ){
            $Sale = Sale::find($request->input('os_no'));
            if ( $Sale == null ) return null;
            $Sale->delete();
            return $Sale;
        }

        return null;
    }

    private function printReceipt($Sale){
        $content = "<align_center>";
        $content .= Config::get('constants.COMPANY_NAME')."\n";
        $content .= Config::get('constants.COMPANY_ADDRESS')."\n";
        $content .= Config::get('constants.COMPANY_CONTACT_NO')."\n\n";
        $content .= "<align_left>";

        $content .= str_pad('ORDER SLIP', $this->size, ' ', STR_PAD_BOTH)."\n\n";

        $content .= "<normal>";
        if (  $Sale->table_id > 0 ) {
            $content .= $this->addHeadings("Table ", $Sale->table->table_desc);
        } else {
            $content .= $this->addHeadings("Table ", "TAKE-OUT");
        }

        if ( isset( $Sale->customer_name ) ) {
            $content .= $this->addHeadings("Customer ", $Sale->customer_name);
        }

        $content .= $this->addHeadings('Date/Time', $Sale->sale_datetime->format("m/d/Y h:i:s A"));
        $content .= $this->addHeadings('Cashier', $Sale->user->name);
        $content .= $this->addHeadings('OS#', str_pad($Sale->id,7,0,STR_PAD_LEFT));
        $content .= $this->divider();

        if ( count($Sale->products) ) {
            foreach( $Sale->products as $item ){
                $content .= $this->addItemWithPrice($item);
            }
        }
        $content .= $this->divider();
        if ( $Sale->gross_amount != $Sale->net_amount ) $content .= $this->addFooter("Gross Amount", $Sale->gross_amount);
        if ( $Sale->vat_discount_amount > 0 ) $content .= $this->addFooter("Less Vat", $Sale->vat_discount_amount);
        if ( $Sale->discount_amount > 0 ) $content .= $this->addFooter("Less Discount", $Sale->discount_amount);
        $content .= $this->addFooter("Amount Due", $Sale->net_amount,true);
        $content .= $this->addFooter("Cash", $Sale->cash_amount);
        $content .= $this->addFooter("Change", $Sale->change_amount,true);
        $content .= "\n\n<align_center><bold>THIS IS NOT YOUR OFFICIAL RECEIPT\n";
        $content .= "<open>";
        $filename = uniqid();
        File::put($filename, $content);
        $this->printFile($filename, OrderController::$MAIN);
        File::delete($filename);

    }

    private function divider(){
        //return str_repeat('-', $this->size).'\n';
        return str_repeat('-', $this->size)."\n";
    }

    private function addHeadings($label,$data){
        $data = str_limit($data, $this->size - $this->label_size,'');
        $content = str_pad($label,$this->label_size - 1,' ', STR_PAD_RIGHT).':';
        $content .= str_pad($data, $this->size - $this->label_size, ' ', STR_PAD_RIGHT);
        $content .= "\n";
        return $content;
    }

    private function addFooter($label,$amount, $tall = false){
        $content = str_pad($label,$this->size - $this->amount_size,' ', STR_PAD_LEFT);
        if ( $tall ) $content .= "<tall>";
        $content .= str_pad(number_format($amount,2), $this->amount_size, ' ', STR_PAD_LEFT);
        if ( $tall ) $content .= "<normal>";
        $content .= "\n";
        return $content;
    }

    private function addContent($label,$data, $tall = false){
        $content = str_pad($label,$this->size - $this->amount_size,' ', STR_PAD_RIGHT);
        if ( $tall ) $content .= "<tall>";
        $content .= str_pad($data, $this->amount_size, ' ', STR_PAD_LEFT);
        if ( $tall ) $content .= "<normal>";
        $content .= "\n";
        return $content;
    }



    private function addItemWithPrice($item){
        $item->product_desc = str_limit($item->product_desc,$this->size - $this->quantity_size,'');

        if ( $item->pivot->quantity > 1 ) {
            $item->product_desc = str_limit($item->product_desc,$this->size - $this->quantity_size - $this->amount_size,'');

            $content = str_pad(round($item->pivot->quantity,0),$this->quantity_size,' ', STR_PAD_RIGHT);
            $content .= str_pad('@ '.number_format($item->pivot->price,2), $this->size - $this->quantity_size, ' ', STR_PAD_RIGHT)."\n";

            $content .= str_pad('',$this->quantity_size,' ', STR_PAD_RIGHT);
            $content .= str_pad($item->product_desc, $this->size - $this->quantity_size - $this->amount_size, ' ', STR_PAD_RIGHT);
            $content .= str_pad(number_format($item->pivot->quantity * $item->pivot->price,2), $this->amount_size, ' ', STR_PAD_LEFT)."\n";

        } else {
            $item->product_desc = str_limit($item->product_desc,$this->size - $this->quantity_size - $this->amount_size,'');
            $content = str_pad($item->pivot->quantity,$this->quantity_size,' ', STR_PAD_RIGHT);
            $content .= str_pad($item->product_desc, $this->size - $this->quantity_size - $this->amount_size, ' ', STR_PAD_RIGHT);
            $content .= str_pad(number_format($item->pivot->quantity * $item->pivot->price,2), $this->amount_size, ' ', STR_PAD_LEFT)."\n";

        }

        /**
         * check if there is sub item
         */
        $sub_items = Product::find($item->id)
            ->subItems()->get();

        if ( count($sub_items) ){
            foreach ( $sub_items as $sub ){
                $sub['product_desc'] = str_limit($sub['product_desc'], $this->size - $this->quantity_size - $this->amount_size,'');
                $content .= str_pad('',$this->quantity_size,' ',STR_PAD_RIGHT);
                $content .= str_pad('-'.$sub['product_desc'], $this->size - $this->quantity_size - $this->amount_size, ' ', STR_PAD_RIGHT)."\n";
            }
        }

        return $content;
    }

    public function xread(Request $request){
        /**
         * check xread
         */
        $Xread = Xread::where(DB::raw('date(xread_datetime)'),Carbon::now()->toDateString())
        ->orderBy('id','desc')
        ->first();

        $now = Carbon::now();

        if ( $Xread == null ) {
            /**
             * first xread
             */

            //dd($now->toDateString() .' | '.  $now->toTimeString());
            $Sale = Sale::where(DB::raw('date(sale_datetime)'),$now->toDateString())
                ->where(DB::raw('time(sale_datetime)'), '<=' ,$now->toTimeString());
        } else {
            $Sale = Sale::where(DB::raw('date(sale_datetime)'),$now->toDateString())
                ->where(DB::raw('time(sale_datetime)'), '>' ,$Xread->xread_datetime->toTimeString())
                ->where(DB::raw('time(sale_datetime)'), '<=' ,$now->toTimeString());


        }

        if ( $Sale->count() <= 0 ){

            if ( $Xread == null ) {
                $invoice_from = 0;
                $invoice_to = 0;
            } else {
                $invoice_from = $Xread->max('invoice_to');
                $invoice_to = $Xread->max('invoice_to');
            }

            $Xread_new = Xread::create([
                'xread_datetime' => $now->toDateTimeString(),
                'cashier_id' => $request->input('id'),
                'gross_amount' => 0,
                'vat_discount_amount' => 0,
                'discount_amount' => 0,
                'net_amount' => 0,
                'invoice_from' => $invoice_from,
                'invoice_to' => $invoice_to
            ]);
        }  else {

            $Xread_new = Xread::create([
                'xread_datetime' => $now->toDateTimeString(),
                'cashier_id' => $request->input('id'),
                'gross_amount' => $Sale->sum('gross_amount'),
                'vat_discount_amount' => $Sale->sum('vat_discount_amount'),
                'discount_amount' => $Sale->sum('discount_amount'),
                'net_amount' => $Sale->sum('net_amount'),
                'invoice_from' => $Sale->min('id'),
                'invoice_to' => $Sale->max('id')
            ]);
        }

        $this->printXread($Xread_new->id);

        return $request->all();
    }

    private function printXread($id){
        $Xread = Xread::find($id);

        $content = "<align_center>";
        $content .= Config::get('constants.COMPANY_NAME')."\n";
        $content .= Config::get('constants.COMPANY_ADDRESS')."\n";
        $content .= Config::get('constants.COMPANY_CONTACT_NO')."\n\n";
        $content .= "<align_left>";

        $content .= "<align_center><fat>XREAD\n\n<align_left><normal>";

        $content .= $Xread->cashier->name."\n";
        $content .= $Xread->xread_datetime->toDateTimeString()."\n";
        $content .= $this->addContent("Ref",$Xread->id);
        $content .= $this->divider();
        $content .= $this->addContent("Gross Amount",number_format($Xread->gross_amount,2));
        $content .= $this->addContent("Vat Discount",number_format($Xread->vat_discount_amount,2));
        $content .= $this->addContent("Discount Amount",number_format($Xread->discount_amount,2));
        $content .= $this->addContent("Net Amount",number_format($Xread->net_amount,2));
        $content .= "Invoice From " . $Xread->invoice_from . ' to ' . $Xread->invoice_to."\n";
        $content .= $this->divider();

        $filename = uniqid();
        File::put($filename, $content);
        $this->printFile($filename, OrderController::$MAIN);
        File::delete($filename);
    }

    private function printZread($id){
        $Zread = Zread::find($id);

        $content = "<align_center>";
        $content .= Config::get('constants.COMPANY_NAME')."\n";
        $content .= Config::get('constants.COMPANY_ADDRESS')."\n";
        $content .= Config::get('constants.COMPANY_CONTACT_NO')."\n\n";
        $content .= "<align_left>";

        $content .= "<align_center><fat>ZREAD\n\n<align_left><normal>";

        $content .= $Zread->cashier->name."\n";
        $content .= $Zread->zread_datetime->toDateTimeString()."\n";
        $content .= $this->addContent("Ref",$Zread->id);
        $content .= $this->divider();
        $content .= $this->addContent("Gross Amount",number_format($Zread->gross_amount,2));
        $content .= $this->addContent("Vat Discount",number_format($Zread->vat_discount_amount,2));
        $content .= $this->addContent("Discount Amount",number_format($Zread->discount_amount,2));
        $content .= $this->addContent("Net Amount",number_format($Zread->net_amount,2));
        $content .= "Invoice From " . $Zread->invoice_from . ' to ' . $Zread->invoice_to."\n";
        $content .= $this->divider();

        $filename = uniqid();
        File::put($filename, $content);
        $this->printFile($filename, OrderController::$MAIN);
        File::delete($filename);
    }

    public function zread(Request $request){
        /**
         * check xread
         */

        $now = Carbon::now();
        $Sale = Sale::where(DB::raw('date(sale_datetime)'),$now->toDateString());

        if ( $Sale->count() <= 0 ){
            /**
             * return null for unable to zread
             */
            return null;
        }  else {
            $Zread_new = Zread::create([
                'zread_datetime' => $now->toDateTimeString(),
                'cashier_id' => $request->input('id'),
                'gross_amount' => $Sale->sum('gross_amount'),
                'vat_discount_amount' => $Sale->sum('vat_discount_amount'),
                'discount_amount' => $Sale->sum('discount_amount'),
                'net_amount' => $Sale->sum('net_amount'),
                'invoice_from' => $Sale->min('id'),
                'invoice_to' => $Sale->max('id')
            ]);
        }

        $this->printZread($Zread_new->id);

        return $request->all();
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
