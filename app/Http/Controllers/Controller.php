<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Validator;

class Controller extends RoutingController {
    public function ReqValidate($req, $rules) {
        $validate = Validator::make($req->all(), $rules);

        if($validate->fails()) {
            return response()->json([
                'message' => 'Invalid fields',
                'errors' =>  $validate->errors()
            ], 422);
        }

        return null;
    }
}
