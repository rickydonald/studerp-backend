<?php

namespace App\Controllers;

use Leaf\Helpers\Authentication;

class ForgotPasswordController extends Controller
{
    /** Method to send one time passcode */
    public function sendOneTimePasscode()
    {
        $registerNumber = request()->get('register_number');
        $mobileNumber = request()->get('mobile_number');

        // Validation
        if (!$registerNumber) {
            response()->json([
                'status' => false,
                'error' => 'username-required',
                'message' => 'Username is required'
            ]);
            exit();
        }
        if (!$mobileNumber) {
            response()->json([
                'status' => false,
                'error' => 'mobile-required',
                'message' => 'Mobile is required'
            ]);
            exit();
        }

        $registerNumber = strtolower($registerNumber);
        $registerNumber = preg_replace('/\s+/', '', $registerNumber);

        $data = db()
            ->select("student_information", "register_number, phone_number")
            ->where([
                "register_number" => strtolower($registerNumber),
                "phone_number" => $mobileNumber
            ])
            ->fetchAll();

        if (!$data) {
            response()->json([
                'status' => false,
                'error' => 'user-not-found',
                'message' => 'User not found'
            ]);
            exit();
        }

        $smsController = SMSController::sendOneTimePasscode($mobileNumber);
        if (!$smsController['status']) {
            response()->json([
                'status' => false,
                'error' => 'otp-send-failed',
                'message' => 'OTP send failed',
                'data' => $smsController
            ]);
            exit();
        }

        $otp = $smsController['data']['otp'];

        // Save OTP
        $update = db()
            ->update("student_information")
            ->params(["temporary_otp" => $otp])
            ->where("register_number", $registerNumber)
            ->execute();

        if (!$update) {
            response()->json([
                'status' => false,
                'error' => 'otp-save-failed',
                'message' => 'OTP save failed'
            ]);
            exit();
        }

        response()->json([
            'status' => true,
            "response" => "otp-sent",
            'message' => 'OTP sent successfully',
            'data' => $data[0],
        ]);
    }

    /** Method to confirm one time passcode */
    public function confirmOneTimePasscode()
    {
        $registerNumber = request()->get('register_number');
        $otp = request()->get('otp');

        // Validation
        if (!$registerNumber) {
            response()->json([
                'status' => false,
                'error' => 'username-required',
                'message' => 'Username is required'
            ]);
            exit();
        }
        if (!$otp) {
            response()->json([
                'status' => false,
                'error' => 'otp-required',
                'message' => 'OTP is required'
            ]);
            exit();
        }

        $registerNumber = strtolower($registerNumber);
        $registerNumber = preg_replace('/\s+/', '', $registerNumber);

        $data = db()
            ->select("student_information", "register_number, temporary_otp")
            ->where([
                "register_number" => strtolower($registerNumber),
            ])
            ->fetchObj();

        if ($data->temporary_otp !== md5($otp)) {
            response()->json([
                'status' => false,
                'error' => 'otp-invalid',
                'message' => 'Invalid OTP',
                'data' => $data
            ]);
            exit();
        }
        
        response()->json([
            'status' => true,
            "response" => "otp-confirmed",
            'message' => 'OTP confirmed successfully',
            'data' => $data
        ]);
    }

    /** Method to change password through recovery */
    public function changePassword()
    {
        $registerNumber = request()->get('register_number');
        $passwordInput = request()->get('new_password');
        $confirmPasswordInput = request()->get('confirm_password');

        if ($passwordInput !== $confirmPasswordInput) {
            response()->json([
                'status' => false,
                'error' => 'password-mismatch',
                'message' => 'Passwords do not match'
            ]);
            exit();
        }

        $update = db()
            ->update("student_information")
            ->params(["password" => md5($passwordInput)])
            ->where("register_number", $registerNumber)
            ->execute();

        if (!$update) {
            response()->json([
                'status' => false,
                'error' => 'password-update-failed',
                'message' => 'Password update failed'
            ]);
            exit();
        }

        response()->json([
            'status' => true,
            "response" => "password-updated",
            'message' => 'Password updated successfully'
        ]);
    }
}
