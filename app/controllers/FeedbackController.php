<?php

namespace App\Controllers;

class FeedbackController extends Controller
{
    public function create()
    {
        $registerNumber = request()->get("register_number");
        $feedback = request()->get("feedback");

        $data = db()
            ->insert("student_feedback")
            ->params([
                "feedback_id" => random_int(100000, 999999),
                "register_number" => $registerNumber,
                "feedback_content" => $feedback,
                "created_at" => time(),
            ])
            ->execute();

        if ($data) {
            response()->json([
                "status" => true,
                "message" => "Feedback submitted successfully",
            ]);
        } else {
            response()->json([
                "status" => false,
                "message" => "Failed to submit feedback",
            ]);
        }
    }
}
