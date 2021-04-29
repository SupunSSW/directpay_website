<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billname;

class BillerController extends Controller
{
    //
    function index()
    {
        $data=Billname::all();
        \Log::info(json_encode($data));
        return view('billers',['Billnamekey'=>$data]);
        //return DB::select("select * from billnames");
        //return view('billers');

        //http://127.0.0.1:8000
    }

    public function paymentResponse(Request $request) {

        \Log::info('called');
        \Log::info(json_encode($request->all()));



        return ;
    }

    public function accountVerification(Request $account) {



    }



}
