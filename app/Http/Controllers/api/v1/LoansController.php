<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Loan;
use App\Models\User;
use App\Services\ResponseService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class LoansController extends Controller
{
    public function create(Request $req) {
        $validate = $this->ReqValidate($req, [
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date'
        ]);

        if($validate) {
            return $validate;
        }

        $book = Books::find($req->book_id)->first();
        if(!$book) {
            ResponseService::message('Book not found', 404);
        }
        
        if($book->stock <= 0) {
            ResponseService::message('No Stock', 404);
        }

        try {
            DB::beginTransaction();

            $loan = Loan::create([
                'book_id' => $book->id,
                'member_id' => Auth::user()->id,
                'loan_date' => date('Y-m-d'),
                'due_date' => $req->due_date
            ]);

            $book->update([
                'stock' => $book->stock - 1
            ]);

            DB::commit();
            return ResponseService::success('Borrowing successfully', 200, $loan);
        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseService::error('Borrowing failed', 400, $e->getMessage());
        }
    }

    public function bookReturn(Request $req, $id) {
        $loan = Loan::find($id);
        if(!$loan || $loan->status == 'returned') {
            return ResponseService::message('Loan not found', 404);
        }

        try {
            DB::beginTransaction();
            
            $loan->update([
                'return_date' => date('Y-m-d'),
                'status' => 'returned'
            ]);

            $book = Books::find($loan->book_id);
            $book->update([
                'stock' => $book->stock + 1
            ]);

            $dueDate = new DateTime($loan->due_date);
            $today = new DateTime(date('Y-m-d'));

            $late = $today > $dueDate ? date_diff($dueDate, $today)->days : 0;

            DB::commit();
            return ResponseService::success('Book returned successfully', 200, [
                'id' => $loan->id,
                'status' => $loan->status,
                'late_days' => $late
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseService::error('Book failed to return', 400, $e->getMessage());
        }
    }

    public function history(Request $req) {
        $page = $req->input('page') ?? 0;
        $size = $req->input('size') ?? 10;

        $validate = $this->ReqValidate($req, [
            'page' => 'integer|min:0',
            'size' => 'integer|min:1'
        ]);

        if($validate) {
            return $validate;
        }

        $loan = Loan::skip($page * $size)->take($size)->get();

        return response()->json([
            'message' => 'Loan History Successfully retrieved',
            'page' => $page,
            'size' => $size,
            'data' => $loan
        ]);

    }

    public function userHistory(Request $req, $id) {
        $page = $req->input('page') ?? 0;
        $size = $req->input('size') ?? 10;

        $validate = $this->ReqValidate($req, [
            'page' => 'integer|min:0',
            'size' => 'integer|min:1'
        ]);

        if($validate) {
            return $validate;
        }

        try {
            $user = User::findOrFail($id);
            $loan = Loan::where('member_id', $user->id)->skip($page * $size)->take($size)->get();
        return response()->json([
            'message' => 'Loan History Successfully retrieved',
            'page' => $page,
            'size' => $size,
            'data' => $loan
        ]);
        } catch (\Throwable $e) {
            return ResponseService::error('Loan History Failed retrieved', 400, $e->getMessage());
        }
    }

    public function selfHistory(Request $req) {
        $page = $req->input('page') ?? 0;
        $size = $req->input('size') ?? 10;
        $user = Auth::user();

        $validate = $this->ReqValidate($req, [
            'page' => 'integer|min:0',
            'size' => 'integer|min:1'
        ]);

        if($validate) {
            return $validate;
        }

        try {
            $loan = Loan::where('member_id', $user->id)->skip($page * $size)->take($size)->get();
        return response()->json([
            'message' => 'Loan History Successfully retrieved',
            'page' => $page,
            'size' => $size,
            'data' => $loan
        ]);
        } catch (\Throwable $e) {
            return ResponseService::error('Loan History Failed retrieved', 400, $e->getMessage());
        }
    }
}
