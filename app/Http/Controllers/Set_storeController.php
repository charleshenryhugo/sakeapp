<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Set_store;
use App\Purchase;
use App\Store;

class Set_storeController extends Controller
{
    public function search_by_set_name(Request $request){
        $set_name = $request->input('set_name');
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
    }
}
