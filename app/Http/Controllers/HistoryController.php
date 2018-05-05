<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\History;
use DB;
class HistoryController extends Controller
{
    public function index(){
        $histories = DB::table('histories')
                    ->join('products','histories.product_id','=','products.product_id')
                    ->select("histories.*","products.product_link","products.collection_id","products.product_title")
                    ->paginate(25);
        return view('history')->with('histories',$histories);
    }
}
