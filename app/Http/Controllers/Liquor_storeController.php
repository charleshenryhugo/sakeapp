<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Liquor_store;
use App\Store;
use App\Purchase;

class Liquor_storeController extends Controller
{
    public function search_by_item_id(Request $request){
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
    }

    public function union_search(Request $request){
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
    }
}
