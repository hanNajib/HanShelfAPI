<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Shelf;
use App\Services\ResponseService;
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
            return ResponseService::success("Shelf stored successfully", 201, $shelf);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseService::error('Shelf failed to update', 400, $th->getMessage());
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
            return ResponseService::message('Shelf deleted successfully', 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseService::error('Shelf failed to delete', 400, $e->getMessage());
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
            return ResponseService::message('Shelf updated successfully', 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseService::error('Shelf failed to update', 400, $e->getMessage());
        }
    }

    public function get(Request $req) {
        $shelf = Shelf::select(['id', 'name'])->get();
        return ResponseService::success('Shelf successfully retrieved', 200, $shelf);
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
