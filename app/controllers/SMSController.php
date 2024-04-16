<?php

namespace App\Controllers;

use GuzzleHttp\Client;

class SMSController extends Controller
{
    public static function sendOneTimePasscode(int $mobileNumber): array
    {
        $otp = rand(1000, 9999);

        $client = new Client();
        $headers = [
            "authorization" => _env("SMSAPI_KEY"),
            "Content-Type" => "application/x-www-form-urlencoded",
        ];
        $options = [
            "form_params" => [
                "variables_values" => $otp,
                "route" => "otp",
                "numbers" => $mobileNumber,
            ],
            "headers" => $headers,
        ];

        $res = $client->request("POST", _env("SMSAPI_URL"), $options);

        if ($res->getStatusCode() !== 200) {
            return [
                "status" => false,
                "error" => "sms-api-error",
                "message" => "SMS API error",
                "response" => json_decode($res->getBody(), true),
                "status_code" => $res->getStatusCode(),
            ];
        }

        $response = $res->getBody();
        return [
            "status" => true,
            "data" => [
                "otp" => md5($otp),
                "sms" => json_decode($response, true),
            ],
        ];
    }
}
