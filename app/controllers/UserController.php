<?php

namespace App\Controllers;

class UserContoller extends Controller
{
    public function show()
    {
        $registerNumber = request()->get("register_number");

        $data = db()
            ->select("student_information")
            ->where(["register_number" => $registerNumber])
            ->all();
    }
}
