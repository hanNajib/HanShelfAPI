<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $req) {
        $validate = $this->ReqValidate($req, [
            'email' => 'required',
            'password' => 'required|min:8'
        ]);

        if($validate) {
            return $validate;
        }

        $user = User::where('email', $req->email)->first();
        if(!$user || !Hash::check($req->password, $user->password) ) {
            return response()->json([
                'message' => 'Wrong Email or Password',
            ], 400);
        }

        $token = $user->createToken('han_shelf')->plainTextToken;

        return response()->json([
            'message' => 'Login Success',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function register(Request $req) {
        $validate = $this->ReqValidate($req, [
            'name' => 'required|regex:/^[a-zA-Z0-9_.]+$/',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        if($validate) {
            return $validate;
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password)
            ]);

            DB::commit();

            $token = $user->createToken('han_shelf')->plainTextToken;

            return response()->json([
                'message' => 'Register Successfully',
                'token' => $token,
                'user' => $user
            ], 201);

        } catch(Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Register Failed',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    public function logout(Request $req) {
        $user = $req->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Success'
        ], 200);
    }
}
