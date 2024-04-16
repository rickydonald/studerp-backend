<?php

namespace App\Controllers;

class AttendanceController extends Controller
{
    public function show()
    {
        $registerNumber = request()->get('register_number');

        $data = db()
            ->select("student_attendance")
            ->where([
                "register_number" => $registerNumber
            ])
            ->fetchAll();

        $result = [];

        foreach ($data[0] as $key => $value) {
            if (preg_match('/^([a-z])\d_\d$/', $key, $matches)) {
                $category = $matches[1];
                $result[$category][] = $value;
            }
        }

        $response = [
            "status" => "success",
            "data" => $result
        ];

        response()->json($response);
    }
}
