<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookController extends Controller
{
    public function create(Request $req) {
        $validate = $this->ReqValidate($req, [
            'title' => 'required',
            'author' => 'required',
            'genre' => 'nullable',
            'publisher' => 'nullable',
            'shelf_id' => 'nullable|exists:shelf,id',
            'published_year' => 'required|digits:4|integer|min:1900|max:'.(date('Y')+1),
            'stock' => 'required|integer'
        ]);

        if($validate) {
            return $validate;
        }

        try {
            DB::beginTransaction();

            $book = Books::create([
                'title' => $req->title,
                'author' => $req->author,
                'genre' => $req->genre,
                'publisher' => $req->publisher,
                'shelf_id' => $req->shelf_id,
                'published_year' => $req->published_year,
                'stock' => $req->stock
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Book Created Successfully',
                'data' => $book
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Book Create Failed',
                'errors' => $e->getMessage() 
            ], 400);
        }
    }

    public function getAllBook(Request $req) {
        $genre = $req->input('genre');
        $author = $req->input('author');
        $published_year = $req->input('published_year');
        $page = $req->input('page') ?? 0;
        $size = $req->input('size') ?? 10;

        $validate = $this->ReqValidate($req, [
            'genre' => 'exists:books,genre',
            'author' => 'exists:books,author',
            'published_year' => 'digits:4|integer|min:1900|max:'.(date('Y')+1),
            'page' => 'integer|min:0',
            'size' => 'integer|min:1'
        ]);

        if($validate) {
            return $validate;
        }

        $query = Books::query();
        if($genre) {
            $query->where('genre', $genre);
        }
        if($author) {
            $query->where('author', $author);
        }
        if($published_year) {
            $query->where('published_year', $published_year);
        }

        $book = $query->skip($page * $size)->take($size)->get();

        return response()->json([
            'message' => 'Books retrieved successfully',
            'page' => $page,
            'size' => $size,
            'data' => $book
        ]);
    }

    public function update(Request $req, $id) {
        $validate = $this->ReqValidate($req, [
            'title' => 'sometimes|required',
            'author' => 'sometimes|required',
            'genre' => 'sometimes|required',
            'shelf_id' => 'nullable|exists:shelf,id',
            'publisher' => 'sometimes|required',
            'published_year' => 'sometimes|required|digits:4|integer|min:1900|max:'.(date('Y')+1),
            'stock' => 'sometimes|required|integer'
        ]);

        if($validate) {
            return $validate;
        }

        try {
            DB::beginTransaction();

            $book = Books::findOrFail($id);
            $book->update($req->only(['title', 'author', 'genre', 'shelf_id', 'published_year', 'publisher', 'stock']));

            DB::commit();

            return response()->json([
                'message' => 'Book Updated Successfully',
                'data' => $book
            ], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Book Update Failed',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    public function delete(Request $req, $id) {
        $book = Books::find($id);
        if(!$book) {
            return response()->json([
                'message' => 'Book not found'
            ], 404);
        }
        try {
            $book->delete();
            return response()->json([
                'message' => 'Book deleted successfully'
            ], 200);
        } catch(Throwable $e) {
            return response()->json([
                'message' => 'Delete Book Failed'
            ], 400);
        }
    }

    public function search(Request $req) {
        $q = $req->input('_q');

        $validate = $this->ReqValidate($req, [
            '_q' => 'required|string|min:1'
        ]);

        if($validate) {
            return $validate;
        }

        $books = Books::where('title', 'LIKE', "%{$q}%")
            ->orWhere('author', 'LIKE', "%{$q}%")
            ->orWhere('genre', 'LIKE', "%{$q}%")
            ->get();

        return response()->json([
            'message' => 'Books retrieved successfully',
            'data' => $books
        ]);
    }
}
