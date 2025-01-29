<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShelfController extends Controller
{
    public function create(Request $req) {
        $validate = $this->ReqValidate($req, [
            'name' => 'required|unique:shelf,name'
        ]);

        if($validate) {
            return $validate;
        }

        try {
            DB::beginTransaction();

            $shelf = Shelf::create($req->all());

            DB::commit();
            return response()->json([
                'message' => 'Shelf stored successfully'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Shelf failed to save',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    public function delete(Request $req, $id) {
        $shelf = Shelf::where('id', $id)->first();
        if(!$shelf) {
            return response()->json([
                'message' => 'Shelf not found'
            ], 404);
        }
        try {
            DB::beginTransaction();
            $shelf->delete();
            DB::commit();
            return response()->json([
                'message' => 'Shelf Deleted Successfully',
            ], 204);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Shelf failed to delete',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $req, $id) {
        $validate = $this->ReqValidate($req, [
            'name' => 'required|unique:shelf,name,' . $id
        ]);

        if($validate) {
            return $validate;
        }

        $shelf = Shelf::where('id', $id)->first();
        if(!$shelf) {
            return response()->json([
                'message' => 'Shelf not found'
            ], 404);
        }

        try {
            DB::beginTransaction();
            
            $shelf->update($req->all());
            DB::commit();
            return response()->json([
                'message' => 'Shelf updated successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Shelf failed to update',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    public function get(Request $req) {
        $shelf = Shelf::select(['id', 'name'])->get();
        return response()->json([
            'message' => 'Shelf successfully retrieved',
            'data' => $shelf
        ]);
    }

    public function getBook(Request $req, $id) {
        $page = $req->input('page') ?? 0;
        $size = $req->input('size') ?? 10;

        $validate = $this->ReqValidate($req, [
            'page' => 'min:0',  
            'size' => 'min:1',  
        ]);
        if($validate) {
            return $validate;
        }
        
        $shelf = Shelf::find($id);
        $book = Books::where('shelf_id', $id)->skip($page * $size)->take($size)->get();

        return response()->json([
            'message' => 'Books successfully retrieved',
            'page' => $page,
            'size' => $size,
            'shelf' => $shelf->name,
            'books' => $book->makeHidden('shelf_id')
        ], 200);
    }
}
