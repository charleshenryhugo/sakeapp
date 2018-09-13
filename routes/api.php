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
Route::get('omakase', 'SetController@search_by_postcode');
//http://localhost:8000/api/omakase/2778888
//api/omakase?postcode=2778888

Route::get('set_order/{set_name}', 'Set_storeController@search_by_set_name');
//http://localhost:8000/api/set_order/goodSet

Route::get('items/{postcode}&{keyword}&{strength}', 'Liquor_storeController@union_search');
//http://localhost:8000/api/items/3338888&おさけ&5

Route::get('purchase/{item_id}', 'Liquor_storeController@search_by_item_id');
//http://localhost:8000/api/purchase/2


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


