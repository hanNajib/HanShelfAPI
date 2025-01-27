<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Loan;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function popular(Request $req) {
        $size = $req->input('size') ?? 10;
        $page = $req->input('page') ?? 0;

        $validate = $this->ReqValidate($req, [
            'page' => 'min:0',
            'size' => 'min:1'
        ]);

        if($validate) {
            return $validate;
        }

        $book = Books::withCount('borrowed')->orderBy('borrowed_count', 'desc')->skip($page * $size)->take($size)->get();
        return response()->json([
            'page' => $page,
            'size' => $size,
            'data' => $book
        ], 200);
    }
}
