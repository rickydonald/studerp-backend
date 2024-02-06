<?php

/** Middleware Registers */
app()->registerMiddleware('unauthenticated_route', function () {
    echo 'Home middleware';
});
app()->registerMiddleware('authenticated_route', function () {
    echo 'Home middleware';
});

/** API v1 Routes */
app()->group('/v1/api', function () {
    /** Un Authenticated Routes (External) */
    app()->group('/e', ["middleware" => "unauthenticated_route"], function () {
        app()->post('/login', "AuthController@login");
        app()->post('/forgot_password', "AuthController@forgotPassword");
    });

    /** Authenticated Routes (Internal) */
    app()->group('/i', ["middleware" => "authenticated_route", function () {
        /** User Routes */
        app()->group('/user', function () {
            /** Profile Routes */
            app()->group('/profile', function () {
                app()->get('/show', "UserController@show");
                app()->post('/update/password', "UserController@updatePassword");
                app()->delete('/delete', "UserController@delete");
            });
            /** Academic Routes */
            app()->group('/academic', function() {
                app()->get('/show/all', "AcademicController@showAll");
                app()->get('/cia/show', "AcademicController@showCia");
                app()->get('/semester_results/show', "AcademicController@showAllSemesterResults");
                app()->get('/current_paper/show', "AcademicController@showCurrentPapers");
            });
            /** Attendance Routes */
            app()->group('/attendance', function() {
                app()->get('/current/lookup', "AttendanceController@currentAttendance");
                app()->get('/show', "AttendanceController@show");
                app()->post('/request', "AttedanceController@requestAttendanceChange");
                app()->post('/generate_certificate', "AttendanceController@generateCertificate");
                app()->post('/generate_letter', "AttendanceController@generateLetter");
            });
            /** Fees Routes */
            app()->group('/fees', function() {
                app()->get('/show/all', "FeeController@showAll");
                app()->get('/show/paid', "FeeController@showPaid");
                app()->get('/show/unpaid', "FeeController@showUnpaid");
                app()->post('/pay/{payment_id}', "FeeController@initiatePayment");
            });
        });
    }]);
});
