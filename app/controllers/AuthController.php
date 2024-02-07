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
            ->fetchObj();

        $bearer = Authentication::generateToken(
            [
                "iat" => time(),
                "iss" => "localhost",
                "exp" => time() + ($this->defaultBearerTokenExpiry ?? (60 * 60 * 24)),
                "user_id" => $data->register_number,
                "data" => $data
            ],
            _env("JWT_SECRET")
        );

        $response = [
            "status" => true,
            "message" => "Login successful",
            "data" => $data,
            "token" => $bearer
        ];

        response()->json($response);
    }

    /** Method to recover user password */
    public function forgotPassword()
    {
        $currentPassword = $this->request->get('current_password');
        $passwordInput = $this->request->get('new_password');
        $confirmPasswordInput = $this->request->get('confirm_password');
    }
}
