<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $collections = Collection::all();
        return view('home')->with('collections',$collections);
    }

    // Update status queue 
    public function updateStatusQueue(){
        $collectionId = request()->collection_id;
        $status = request()->status;
        $timeOut = request()->timeout;
        if($timeOut != null){
            $collection = Collection::where('collection_id', $collectionId)->update(
                ['status' => $status,'timeout' => $timeOut]);
        } else {
            $collection = Collection::where('collection_id', $collectionId)->update(
                ['status' => $status]);            
        }

        return response()->json([
            "success" => true,
            "message" => "Update Success"
        ]);
    }   
}
