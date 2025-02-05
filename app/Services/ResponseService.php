<?php

namespace App\Services;

class ResponseService {

    public static function message($message, $http) {
        return response()->json([
            'message' => $message
        ], $http);
    }

    public static function success($message = 'Success', $http = 200, $data = []) {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $http);
    }

    public static function error($message = 'Error', $http = 400, $error = []) {
        return response()->json([
            'message' => $message,
            'errors' => $error
        ], $http);
    }
}