<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return Product::with('subItems','category')->get();
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

        if ( $request->has('product_desc') ) {
            if ( $request->has('id') ) {
                $Product = Product::find($request->input('id'));
                $Product->update([
                    'product_desc' => $request->input('product_desc'),
                    'price' => $request->input('price'),
                    'serving_size' => $request->input('serving_size'),
                    'stock_code' => $request->input('stock_code'),
                    'category_id' => $request->input('category_id')
                ]);
            } else {
                $Product = Product::create([
                    'product_desc' => $request->input('product_desc'),
                    'price' => $request->input('price'),
                    'serving_size' => $request->input('serving_size'),
                    'stock_code' => $request->input('stock_code'),
                    'category_id' => $request->input('category_id')
                ]);
            }

            if ( $request->has('sub_items') ) {
                if ( count( $request->input('sub_items') ) ) {
                    $Product->subItems()->detach();
                    foreach ( $request->input('sub_items') as $item ) {
                        $Product->subItems()->attach($item['id']);
                    }
                }
            }
        }

        return Redirect::to('/products');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return Product::find($id);
    }

    public function stockCode($stock_code){
        return Product::whereStockCode($stock_code)->first();
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
        Product::find($id)->delete();

        return Redirect::to('/products');
    }

    public function deleteItem($product_id, $item_id){
        $Product = Product::find($product_id);
        $Product->subItems()->detach($item_id);

        return $Product->subitems;
    }
}
