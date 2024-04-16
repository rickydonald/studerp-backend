<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;

class NotificationController extends Controller
{
    public function show()
    {
        $registerNumber = request()->get("register_number");

        $data = db()
            ->select("student_notification")
            ->orWhere([
                "register_number" => $registerNumber,
                "notification_for" => "all",
            ])
            ->orderBy("created_at", "desc")
            ->all();

        $res = [
            "status" => true,
            "data" => $data,
        ];

        response()->json($res);
    }

    public function sendPushNotification(string $title, string $description)
    {
        $messages = [
            new ExpoMessage([
                "title" => $title,
                "body" => $description,
                "priority" => "high",
            ]),
        ];

        /**
         * These recipients are used when ExpoMessage does not have "to" set
         */
        $defaultRecipients = ["ExponentPushToken[Gpkod3I4XrvjiYxU4BhhtV]"];

        $response = (new Expo())
            ->send($messages)
            ->to($defaultRecipients)
            ->push();
        return $response->getData();
    }

    public function create()
    {
        $notificationId = bin2hex(random_bytes(10));
        $notificationTitle = request()->get("title");
        $notificationDescription = request()->get("description");
        $notificationFor = request()->get("for");
        $registerNumber = request()->get("register_number");
        $createdAt = time();
        $notificationStatus = "sent";
        $notificationFrom = request()->get("from");

        $create = db()
            ->insert("student_notification")
            ->params([
                "notification_id" => $notificationId,
                "notification_title" => $notificationTitle,
                "notification_description" => $notificationDescription,
                "notification_for" => $notificationFor,
                "created_at" => $createdAt,
                "register_number" => $registerNumber,
                "notification_status" => $notificationStatus,
                "notification_from" => $notificationFrom,
            ])
            ->execute();

        if ($create) {
            $pushStatus = false;
            if (
                $this->sendPushNotification(
                    $notificationTitle,
                    $notificationDescription
                )[0]["status"] === "ok"
            ) {
                $pushStatus = true;
            }
            $res = [
                "status" => true,
                "message" => "Notification sent successfully",
                "push_notify" => $pushStatus ? "success" : "failed",
            ];
        } else {
            $res = [
                "status" => false,
                "message" => "Failed to send notification",
            ];
        }

        response()->json($res);
    }
}
