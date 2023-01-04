<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
    * @param Request $request
    *
    *  @return json
    */

    public function sales(Request $request){

        return $request;

    }
}
