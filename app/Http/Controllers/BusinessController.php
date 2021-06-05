<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessesController extends Controller
{
   public function businessSummary($id)
   {
       $business = Business::find($id);
       $params = array(
            "business" => $business,
            "postImages" => $business->images()->paginate(8),
            "reviews" => $business->reviews()->paginate(8, ['*'], 'rpage')
       );
       
       return view("custom.business-summary", $params);
   }

}
