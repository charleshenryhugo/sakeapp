<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Liquor_store;
use App\Store;
use App\Purchase;

class Liquor_storeController extends Controller
{
    public function search_by_item_ids(Request $request){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        $liquor_store_ids = explode(',', $request->input('item_id'));
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
                    'items'=>$liquor_stores);
    }

    //used fields(columns): stores->postcode, Liquor_store->degree,  Liquor_store->description
    //requirements: 
    public function union_search(Request $request){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        $postcode = $request->input('postcode');
        $keywords = explode(',', $request->input('keyword'));    //array orWhere
        $strengths = explode(',',$request->input('strength'));  //array orWhere
        //strengths: string[]
        //strength-> ('low', 'mid', 'high', 'unknown')
        //degree  -> ( 3~4    5~10,  11~100, 0~100)
        //begin process strengths
        //build a degree_range array [3,4........100]
        $degree_range = array();
        foreach($strengths as $strength){
            switch($strength){
                case 'low':
                    $degree_range = array_merge($degree_range, range(3,4));
                    break;
                case 'mid':
                    $degree_range = array_merge($degree_range, range(5,10));
                    break;
                case 'high':
                    $degree_range = array_merge($degree_range, range(11,100));
                    break;
                default:
            }
        }
        //end process strengths

        //begin process postcode
    
        //first make a store ids list with proper address
        $target_stores = Store::where('postcode', $postcode)->get();
        $target_store_ids = array();
        $i = 0;
        foreach ($target_stores as $store){
            $target_store_ids[$i] = $store->id;
            $i += 1;
        }
        //end process postcode

        //start process keywords
        $liquor_stores = Liquor_store::where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->orWhere('description', 'like', '%'.$keyword.'%');
            }
        })->whereIn('store_id', $target_store_ids)->whereIn('degree', $degree_range)->get();
        //end process keywords

        return array('items'=>$liquor_stores);
    }
}
