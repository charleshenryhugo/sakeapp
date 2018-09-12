<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Set;
use App\Liquor;

class SetController extends Controller
{
    public function search_by_postcode(Request $request)
    {
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
    }
}
