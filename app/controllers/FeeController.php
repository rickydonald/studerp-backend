<?php

use Leaf\Controller;

class FeeController extends Controller
{
    public function getFeeDetailsForStudents()
    {
        $registerNumber = request()->get('register_number');
        $semester = request()->get('semester');

        // Validation
        if (!$registerNumber) {
            response()->json([
                'status' => false,
                'error' => 'username-required',
                'message' => 'Username is required'
            ]);
            exit();
        }
        if (!$semester) {
            response()->json([
                'status' => false,
                'error' => 'semester-required',
                'message' => 'Semester is required'
            ]);
            exit();
        }

        $registerNumber = strtolower($registerNumber);
        $registerNumber = preg_replace('/\s+/', '', $registerNumber);

        $data = db()
            ->select("student_information", "register_number")
            ->where([
                "register_number" => strtolower($registerNumber)
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

        $feeDetails = db()
            ->select("fee_details", "semester, fee_amount")
            ->where([
                "semester" => $semester
            ])
            ->fetchAll();
        if (!$feeDetails) {
            response()->json([
                'status' => false,
                'error' => 'fee-details-not-found',
                'message' => 'Fee details not found'
            ]);
            exit();
        }

        response()->json([
            'status' => true,
            'message' => 'Fee details found',
            'data' => $feeDetails
        ]);
    }
}
