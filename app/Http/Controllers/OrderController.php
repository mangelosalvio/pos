<?php

namespace App\Http\Controllers;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class OrderController extends Controller
{

    public static $KITCHEN = 1;
    public static $MAIN = 0;

    public function __construct(){
        $this->size = 32;
        $this->thermal_size = 40;
        $this->dot_matrix_size = 32;

        $this->label_size = 10;
        $this->quantity_size = 5;
        $this->amount_size = 10;
    }

    private function printFile($filename, $print_location ){

        //if ( $print_location == null ) $print_location = OrderController::$KITCHEN;

        if ( $print_location == 1 ) {
            exec("python /etc/pos/tcp_client.py ".Config::get('constants.PRINTER_IP_KITCHEN')." ".Config::get('constants.PRINTER_PORT')." $filename");
        } else {
            exec("python /etc/pos/tcp_client.py ".Config::get('constants.PRINTER_IP_MAIN')." ".Config::get('constants.PRINTER_PORT')." $filename");
        }

    }

    public function bill(Request $request){
        $data = $request->all();
        $this->size = $this->thermal_size;

        $content = "<align_center>";
        $content .= Config::get('constants.COMPANY_NAME')."\n";
        $content .= Config::get('constants.COMPANY_ADDRESS')."\n";
        $content .= Config::get('constants.COMPANY_CONTACT_NO')."\n\n";
        $content .= "<align_left>";

        $content .= str_pad('BILL', $this->size, ' ', STR_PAD_BOTH)."\n\n";

        if ( isset( $data['desc'] ) ) {
            $content .= $this->addHeadings("Table ", $data['desc'],true);
        }

        if ( isset( $data['customer_name'] ) ) {
            $content .= $this->addHeadings("Customer ", $data['customer_name']);
        }

        $content .= $this->addHeadings('Date/Time', Carbon::now()->format("m/d/Y h:i:s A"));
        $content .= $this->divider();

        $gross_amount = $net_amount = $vat_discount = $discount_amount = 0;

        if ( count( $data['orders'] ) ) {
            foreach ( $data['orders'] as $order ) {
                if ( count($order['details']) ) {
                    foreach ( $order['details'] as $item ) {
                        $content .= $this->addItemWithPrice($item);
                        $gross_amount += $item['quantity'] * $item['price'];
                        $net_amount += $item['amount'];
                        $vat_discount += $item['vat_disc'];
                        $discount_amount += $item['disc'];
                    }
                }
            }
        }

        $content .= $this->divider();
        if ( $gross_amount != $net_amount ) $content .= $this->addFooter("Gross Amount", $gross_amount);
        if ( $vat_discount > 0 ) $content .= $this->addFooter("Less Vat", $vat_discount);
        if ( $discount_amount > 0 ) $content .= $this->addFooter("Less Discount", $discount_amount);
        $content .= $this->addFooter("Amount Due", $net_amount, true);

        $filename = uniqid();
        File::put($filename, $content);
        $this->printFile($filename, 0);
        File::delete($filename);
        return $data;

    }

    public function printToKitchen(Request $request){
        $this->size = $this->dot_matrix_size;

        $content = "<align_center>";
        $content .= Config::get('constants.COMPANY_NAME')."\n";
        $content .= Config::get('constants.COMPANY_ADDRESS')."\n";
        $content .= Config::get('constants.COMPANY_CONTACT_NO')."\n\n";
        $content .= "<align_left>";

        $content .= str_pad('ORDER SLIP', $this->size, ' ', STR_PAD_BOTH)."\n\n";
        $data = $request->all();
        /**
         * headings
         */
        if ( isset( $data['table_desc'] ) ) {
            $content .= $this->addHeadings("Table ", $data['table_desc'],true);
        }

        if ( isset( $data['customer_name'] ) ) {
            $content .= $this->addHeadings("Customer ", $data['customer_name']);
        }
        $content .= $this->addHeadings('Date/Time', Carbon::now()->format("m/d/Y h:i:s A"));
        $content .= $this->divider();

        if ( count($data['details']) ) {
            foreach ( $data['details'] as $item ) {
                //dd($item);
                $content .= $this->addItem($item);
            }
        }

        $content .= $this->divider();
        $content .= str_pad("Remarks: ".$data['remarks'],$this->size,' ',STR_PAD_RIGHT)."\n";
        $content .= $this->divider();


        $filename = uniqid();
        File::put($filename, $content);
        $this->printFile($filename, self::$KITCHEN);
        File::delete($filename);
        return $request->all();
    }

    private function divider(){
        //return str_repeat('-', $this->size).'\n';
        return str_repeat('-', $this->size)."\n";
    }

    private function addHeadings($label,$data,$tall = false){
        $content = str_pad($label,$this->label_size - 1,' ', STR_PAD_RIGHT).':';
        if ( $tall ) $content .= "<tall>";
        $content .= str_pad($data, $this->size - $this->label_size, ' ', STR_PAD_RIGHT);
        if ( $tall ) $content .= "<normal>";
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

    private function addItem($item){
        $item['product_desc'] = str_limit($item['product_desc'],$this->size - $this->quantity_size,'');
        $content = str_pad($item['quantity'],$this->quantity_size,' ', STR_PAD_RIGHT);
        $content .= str_pad($item['product_desc'], $this->size - $this->quantity_size, ' ', STR_PAD_RIGHT);
        $content .= "\n";

        /**
         * check if there is sub item
         */
        $sub_items = Product::find($item['id'])
            ->subItems()->get();

        if ( count($sub_items) ){
            foreach ( $sub_items as $sub ){
                $content .= str_pad('',$this->quantity_size,' ',STR_PAD_RIGHT);
                $content .= str_pad('-'.$sub['product_desc'], $this->size - $this->quantity_size, ' ', STR_PAD_RIGHT)."\n";
            }
        }

        return $content;
    }

    private function addItemWithPrice($item){
        $item['product_desc'] = str_limit($item['product_desc'],$this->size - $this->quantity_size,'');

        if ( $item['quantity'] > 1 ) {
            $item['product_desc'] = str_limit($item['product_desc'],$this->size - $this->quantity_size - $this->amount_size,'');

            $content = str_pad($item['quantity'],$this->quantity_size,' ', STR_PAD_RIGHT);
            $content .= str_pad('@ '.number_format($item['price'],2), $this->size - $this->quantity_size, ' ', STR_PAD_RIGHT)."\n";

            $content .= str_pad('',$this->quantity_size,' ', STR_PAD_RIGHT);
            $content .= str_pad($item['product_desc'], $this->size - $this->quantity_size - $this->amount_size, ' ', STR_PAD_RIGHT);
            $content .= str_pad(number_format($item['quantity'] * $item['price'],2), $this->amount_size, ' ', STR_PAD_LEFT)."\n";

        } else {
            $item['product_desc'] = str_limit($item['product_desc'],$this->size - $this->quantity_size - $this->amount_size,'');
            $content = str_pad($item['quantity'],$this->quantity_size,' ', STR_PAD_RIGHT);
            $content .= str_pad($item['product_desc'], $this->size - $this->quantity_size - $this->amount_size, ' ', STR_PAD_RIGHT);
            $content .= str_pad(number_format($item['quantity'] * $item['price'],2), $this->amount_size, ' ', STR_PAD_LEFT)."\n";

        }

        /**
         * check if there is sub item
         */
        $sub_items = Product::find($item['id'])
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

    public function cancelItem( Request $request ){
        $this->size = $this->dot_matrix_size;

        $content = "<align_center>";
        $content .= Config::get('constants.COMPANY_NAME')."\n";
        $content .= Config::get('constants.COMPANY_ADDRESS')."\n";
        $content .= Config::get('constants.COMPANY_CONTACT_NO')."\n\n";
        $content .= "<align_left>";

        $content .= str_pad('ORDER SLIP', $this->size, ' ', STR_PAD_BOTH)."\n\n";
        $data = $request->all();
        /**
         * headings
         */
        if ( isset( $data['table_desc'] ) ) {
            $content .= $this->addHeadings("Table ", $data['table_desc'],true);
        }

        if ( isset( $data['customer_name'] ) ) {
            $content .= $this->addHeadings("Customer ", $data['customer_name']);
        }
        $content .= $this->addHeadings('Date/Time', Carbon::now()->format("m/d/Y h:i:s A"));
        $content .= "<align_center><fat>CANCEL ORDER<normal>\n<align_left>";
        $content .= $this->divider();
        $content .= "<align_left>".$this->addItem($data['item']);
        $content .= $this->divider();

        $filename = uniqid();
        File::put($filename, $content);
        $this->printFile($filename, self::$KITCHEN);
        File::delete($filename);
    }



    public function cancelOrder( Request $request ){
        $this->size = $this->dot_matrix_size;

        $content = "<align_center>";
        $content .= Config::get('constants.COMPANY_NAME')."\n";
        $content .= Config::get('constants.COMPANY_ADDRESS')."\n";
        $content .= Config::get('constants.COMPANY_CONTACT_NO')."\n\n";
        $content .= "<align_left>";

        $content .= str_pad('ORDER SLIP', $this->size, ' ', STR_PAD_BOTH)."\n\n";
        $data = $request->all();
        /**
         * headings
         */
        if ( isset( $data['table_desc'] ) ) {
            $content .= $this->addHeadings("Table ", $data['table_desc'],true);
        }

        if ( isset( $data['customer_name'] ) ) {
            $content .= $this->addHeadings("Customer ", $data['customer_name']);
        }
        $content .= $this->addHeadings('Date/Time', Carbon::now()->format("m/d/Y h:i:s A"));
        $content .= "<align_center><fat>CANCEL ORDER<normal>\n<align_left>";
        $content .= $this->divider();

        $content .= "<align_left>";
        if ( count($data['order']['details']) ) {
            foreach ( $data['order']['details'] as $item ) {
                $content .= $this->addItem($item);
            }
        }
        $content .= $this->divider();

        $filename = uniqid();
        File::put($filename, $content);
        $this->printFile($filename, self::$KITCHEN);
        File::delete($filename);
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
        //
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
