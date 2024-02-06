<?php

namespace App\Controllers;

class AuthController extends Controller
{
    /** Method to authenticate and user login */
    public function login()
    {
        $registerNumber = $this->request->get('register_number');
        $password = $this->request->get('password');
    }

    /** Method to recover user password */
    public function forgotPassword()
    {
        $currentPassword = $this->request->get('current_password');
        $passwordInput = $this->request->get('new_password');
        $confirmPasswordInput = $this->request->get('confirm_password');
    }
}