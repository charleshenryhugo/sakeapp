<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Set_store;
use App\Purchase;
use App\Store;
use App\Set;
use App\Liquor_store;

class Set_storeController extends Controller
{
    public function search_by_set_id(Request $request){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        
        $set_id = $request->input('set_id');
        $set = Set::find((int)$set_id);
        
        $liquor_store_ids = [$set->liquor1_id, $set->liquor2_id, $set->liquor3_id, $set->liquor4_id, $set->liquor5_id];
        
        $liquor_stores = array();
        $i = 0;
        foreach($liquor_store_ids as $liquor_store_id){
            $liquor_stores[$i] = Liquor_store::find($liquor_store_id);
            $i += 1;
        }

        while($i < 5){
            $liquor_stores[$i] = Liquor_store::find(rand($i+1, 19));
            $i += 1;
        }

        $stores = array();
        $i = 0;
        foreach($liquor_stores as $liquor_store){
            $stores[$i] = Store::find($liquor_store->store_id);
            $i += 1;
        }
        
        $purchase_id = 'XOMHB';

        $source = $stores[0]->name; 
        $source_info = $stores[0]->info;
        $source_address = $stores[0]->address;
        $arrival_time = date('m/d/Y H:i:s', ($stores[0]->delivery_time * 60));
        
        return array('purchase_id'=>$purchase_id, 
                    'source'=>$source, 
                    'source_info'=>$source_info, 
                    'source_address'=>$source_address,
                    'arrival_time'=>$arrival_time,
                    'items'=>array(array('id'=>$set->id, 'name'=>$set->name, 'price'=>$set->set_price, 'thumbnail'=>$set->image_url, 'description'=>$set->description)));
    }
    
    #used fields(columns): Set_stores->name, Stores->(name, info, address, delivery_time)
    public function search_by_set_name(Request $request){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        $set_name = $request->input('set_name');
        $set_store = Set_store::where('name', $set_name)->first();
        $purchase = Purchase::where('set_id', ($set_store->set_id))->first();
        if ($purchase == null){
            return array();
        }
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
