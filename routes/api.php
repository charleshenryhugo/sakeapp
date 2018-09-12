<?php

use Illuminate\Http\Request;
use App\Set;
use App\liquor;
use App\Set_store;
use App\Purchase;
use App\Store;
use App\Liquor_store;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

// GET methods
Route::get('omakase/{postcode}', function(Request $request){
    $postcode = $request->postcode;
    $sets = Set::where('postcode', $postcode)->get();
    $response = array();
    $i = 0;
    foreach ($sets as $set){
        $set_name = $set->name;
        $price = $set->set_price;
        $liquor1 = Liquor::find($set->liquor1_id);
        $liquor2 = Liquor::find($set->liquor2_id);
        $liquor3 = Liquor::find($set->liquor3_id);
        $liquor4 = Liquor::find($set->liquor4_id);
        $liquor5 = Liquor::find($set->liquor5_id);
        $items = [$liquor1,$liquor2,$liquor3,$liquor4,$liquor5];
        $thumbnail = $set->image_url;
        $description = $set->description;
        $response[$i] = ['name'=>$set_name, 'price'=>$price, 'items'=>$items, 'thumbnail'=>$thumbnail, 'description'=>$description];
        $i += 1;
    }
    return array('sets'=>$response);
});

Route::get("set_order/{set_name}", function(Request $request){
    $set_name = $request->set_name;
    $set_store = Set_store::where('name', $set_name)->first();
    $purchase = Purchase::where('set_id', $set_store->set_id)->first();
    $store = Store::where('id', $set_store->store_id)->first();
    $purchase_id = $purchase->id;
    $source = $store->name;
    $source_info = $store->info; //add new col
    $source_address = $store->address; //add new col
    $arrival_time = date('m/d/Y H:i:s', ($purchase->created_at->timestamp + $store->delivery_time * 60));

    $response = array('purchase_id'=>$purchase_id, 'source'=>$source, 'source_info'=>$source_info, 'source_address'=>$source_address, 'arrival_time'=>$arrival_time);

    return $response;
});

Route::get("items/{postcode}&{keyword}&{strength}", function(Request $request){
    $postcode = $request->postcode; 
    $keyword = $request->keyword;
    $degree = $request->strength;
    
    $post_x = $postcode[2];
    $post_y = $postcode[3];

    //first make a store ids list with proper address
    $target_stores = Store::where('addr_x', $post_x)->where('addr_y', $post_y)->get();
    $target_store_ids = array();
    $i = 0;
    foreach ($target_stores as $store){
        $target_store_ids[$i] = $store->id;
        $i += 1;
    }
    
    $liquor_stores = Liquor_store::where('description', 'LIKE', '%'.$keyword.'%')
                            ->where('degree', $degree)->whereIn('store_id', $target_store_ids)->get();

    //add image_url and degree columns to liquor_stores table
    return array('items'=>$liquor_stores);
});

Route::get('purchase/{item_id}', function(Request $request){
    $liquor_store_id = $request->item_id;
    $liquor_store = Liquor_store::find($liquor_store_id);
    $purchase = Purchase::where('liquor1_id', $liquor_store_id)
                        ->orWhere('liquor2_id', $liquor_store_id)
                        ->orWhere('liquor3_id', $liquor_store_id)
                        ->orWhere('liquor4_id', $liquor_store_id)
                        ->orWhere('liquor5_id', $liquor_store_id)
                        ->first();

    $store = Store::find($liquor_store->store_id);
    $purchase_id = $purchase->id;
    $source = $store->name;
    $source_info = $store->info; //add new col
    $source_address = $store->address; //add new col
    $arrival_time = date('m/d/Y H:i:s', ($purchase->created_at->timestamp + $store->delivery_time * 60));

    $response = array('purchase_id'=>$purchase_id, 'source'=>$source, 'source_info'=>$source_info, 'source_address'=>$source_address, 'arrival_time'=>$arrival_time);

    return $response;
});

// POST methods
Route::post('pay/credit', function(Request $request){
    //$requestArray = $request->all();
    $purchase_id = $request->input('purchase_id');
    $code = $request->input('security_code');
    
    $purchase = Purchase::where('code', $code)->orWhere('id', $purchase_id)->first();
    //dd($purchase);
    return array('purchase_id'=>$purchase->id);
});

Route::post('purchase', function(Request $request){
    $purchase_id = $request->input('purchase_id');
    $purchase = Purchase::find($purchase_id);
    $liquor_store_ids = array($purchase->liquor1_id, $purchase->liquor2_id, $purchase->liquor3_id, $purchase->liquor4_id, $purchase->liquor5_id);
    $liquor_stores = Liquor_store::whereIn('id', $liquor_store_ids)->get();
    
    return array('items'=>$liquor_stores);
});


