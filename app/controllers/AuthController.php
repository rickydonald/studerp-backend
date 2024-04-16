<?php

namespace App\Controllers;

use Leaf\Helpers\Authentication;

class AuthController extends Controller
{
    private int $defaultBearerTokenExpiry = 2628000;

    /** Method to authenticate and user login */
    public function login()
    {
        $registerNumber = request()->get('register_number');
        $password = request()->get('password');

        // Validation
        if (!$registerNumber) {
            response()->json([
                'status' => false,
                'error' => 'username-required',
                'message' => 'Username is required'
            ]);
            exit();
        }
        if (!$password) {
            response()->json([
                'status' => false,
                'error' => 'password-required',
                'message' => 'Password is required'
            ]);
            exit();
        }

        $registerNumber = strtolower($registerNumber);
        $registerNumber = preg_replace('/\s+/', '', $registerNumber);

        $data = db()
            ->select("student_information")
            ->where([
                "register_number" => strtolower($registerNumber),
                "password" => md5($password)
            ])
            ->hidden("password")
            ->fetchAll();

        if (!$data) {
            response()->json([
                'status' => false,
                'error' => 'user-not-found',
                'message' => 'Invalid password, retry again!'
            ]);
            exit();
        }

        $bearer = Authentication::generateToken(
            [
                "iat" => time(),
                "iss" => "localhost",
                "exp" => time() + ($this->defaultBearerTokenExpiry ?? (60 * 60 * 24)),
                "user_id" => $data[0]['register_number'],
                "data" => $data[0]
            ],
            _env("JWT_SECRET")
        );

        $response = [
            "status" => true,
            "message" => "Login successful",
            "data" => $data[0],
            "token" => $bearer
        ];

        response()->json($response);
    }
}
