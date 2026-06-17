<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionType;

class SubscriptionTypesController extends Controller
{
    //


    public function index(){
        $types = SubscriptionType::select('id','duration', 'price' , 'duration_unit')->get();

        return response()->json($types);
    }
}
