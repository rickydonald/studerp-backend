<?php

/** API v1 Routes */
app()->group("/v1/api", function () {
    /** Un Authenticated Routes (External) */
    app()->group("/e", [
        "middleware" => "unauthenticated_route",
        function () {
            app()->post("/login", "AuthController@login");
            app()->group("/forgot_password", function () {
                app()->post(
                    "/request",
                    "ForgotPasswordController@sendOneTimePasscode"
                );
                app()->post(
                    "/verify_passcode",
                    "ForgotPasswordController@confirmOneTimePasscode"
                );
                app()->post(
                    "/reset_password",
                    "ForgotPasswordController@changePassword"
                );
            });
        },
    ]);

    /** Authenticated Routes (Internal) */
    app()->group("/i", [
        "middleware" => "authenticated_route",
        function () {
            /** User Routes */
            app()->group("/user", function () {
                /** Profile Routes */
                app()->group("/profile", function () {
                    app()->get("/show", "UserController@show");
                    app()->post(
                        "/update/password",
                        "UserController@updatePassword"
                    );
                    app()->delete("/delete", "UserController@delete");
                });
                /** Academic Routes */
                app()->group("/academic", function () {
                    app()->get("/show/all", "AcademicController@showAll");
                    app()->get("/cia/show", "AcademicController@showCia");
                    app()->get(
                        "/semester_results/show",
                        "AcademicController@showAllSemesterResults"
                    );
                    app()->get(
                        "/current_paper/show",
                        "AcademicController@showCurrentPapers"
                    );
                });
                /** Attendance Routes */
                app()->group("/attendance", function () {
                    app()->get(
                        "/current/lookup",
                        "AttendanceController@currentAttendance"
                    );
                    app()->get("/show", "AttendanceController@show");
                    app()->post(
                        "/request",
                        "AttedanceController@requestAttendanceChange"
                    );
                    app()->post(
                        "/generate_certificate",
                        "AttendanceController@generateCertificate"
                    );
                    app()->post(
                        "/generate_letter",
                        "AttendanceController@generateLetter"
                    );
                });
                /** Fees Routes */
                app()->group("/fees", function () {
                    app()->get("/show/all", "FeeController@showAll");
                    app()->get("/show/paid", "FeeController@showPaid");
                    app()->get("/show/unpaid", "FeeController@showUnpaid");
                    app()->post(
                        "/pay/{payment_id}",
                        "FeeController@initiatePayment"
                    );
                });
                /** Notification Routes */
                app()->group("/notifications", function () {
                    app()->get("/show", "NotificationController@show");
                    app()->post("/create", "NotificationController@create");
                });
                /** Feedback Routes */
                app()->group("/feedback", function () {
                    app()->post("/create", "FeedbackController@create");
                });
            });
        },
    ]);
});
