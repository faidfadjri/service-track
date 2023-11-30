<?php

namespace App\Http\Repositories\Notification;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

interface whatsappInterface
{
    public static function sendCompleteNotification(
        Vehicle $vehicle,
        Customer $customer,
        String $to_name,
        String $to_number,
        String $jobId,
        String $policeNumber,
        String $jobType
    ): Response;
}

class Whatsapp implements whatsappInterface
{
    public static function sendCompleteNotification(
        Vehicle $vehicle,
        Customer $customer,
        String $to_name,
        String $to_number,
        String $jobId,
        String $policeNumber,
        String $jobType
    ): Response {

        $headers = ["Authorization" => "Bearer " . getenv("QONTAK_CLIENT_SECRET")];
        $response = Http::withHeaders($headers)->post(getenv("QONTAK_CLIENT_BASE_URL") . "api/send-message", [
            "to_name"             => $to_name,
            "to_number"           => $to_number,
            "message_template_id" => getenv("QONTAK_MESSAGE_TEMPLATE_ID"),
            "params"              => [
                [
                    "key" => 1,
                    "value" => "full_name",
                    "value_text" => $customer->CustomerName
                ],
                [
                    "key" => 2,
                    "value" => "nopol",
                    "value_text" => $policeNumber
                ],
                [
                    "key" => 3,
                    "value" => "pekerjaan",
                    "value_text" => $jobType
                ]
            ]
        ]);

        return $response;
    }
}
