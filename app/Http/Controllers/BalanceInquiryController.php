<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class balanceInquiryController extends Controller
{

    public const Headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiI3MTMwYjRkZC1kNzM1LTQxYWYtODI5Ny1lMmIwMGE3ZjM3ODQiLCJjbGllbnRJZCI6ImUwNGExMTA1MTk3MTRjYWJhY2ZjYjkxOWQzNWEwYjA3IiwibmJmIjoxNzQyOTk2NjY2LCJleHAiOjE3NDI5OTc1NjYsImlhdCI6MTc0Mjk5NjY2Nn0.jc4AkqE_U_MDsQfHm61lGtxASdNmw1cnC0Qf0Feg96U',
        'X-TIMESTAMP' => '2025-03-26T12:20:00+07:00',
        'X-SIGNATURE' => 'Y8a0K2qeGOTtWF5BgJ0lBWHu1wyjKry/zzuvMD11P/lrV3HObJCE3urcdcQ9XBVbbAnf3yV7ts7u2L2lW7Tv0A==',
        'X-PARTNER-ID' => 'e04a110519714cabacfcb919d35a0b07',
        'X-EXTERNAL-ID' => '41807553358950093184162180797837',
        'CHANNEL-ID' => '95221',
    ];

    public function balanceInquiry()
    {
        $clientId = env('CLIENT_ID');
        $clientSecret = env('CLIENT_SECRET');
        $publicKey = env('PUBLIC_KEY');
        $privateKey = env('PRIVATE_KEY');
        $externalId = env('EXTERNAL_ID');
        $channelId = env('CHANNEL_ID');

        $timestamp = '2025-03-26T12:20:00+07:00';

        try {
            $signature_auth = $this->getSignAuth($timestamp, $clientId, $privateKey);

            $access_token = $this->getAccessToken($timestamp, $clientId, $signature_auth);

            $signature_service = $this->getSignService($timestamp, $clientSecret, $access_token);

            // dd($access_token, $signature_service);

            $balance_info = $this->getBalanceInfo($timestamp, $clientId, $access_token, $signature_service, $externalId, $channelId);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'success',
            'data' => $balance_info,
            'statusCode' => Response::HTTP_OK,
        ], Response::HTTP_OK);
    }

    private function getSignAuth($timestamp, $clientId, $privateKey)
    {
        $headers = [
            'X-TIMESTAMP' => $timestamp,
            'X-CLIENT-KEY' => $clientId,
            'Private_Key' => $privateKey,
        ];

        $response = Http::withHeaders($headers)
            ->post('https://apidevportal.aspi-indonesia.or.id:44310/api/v1.0/utilities/signature-auth');

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'error' => $response->json(),
            ], $response->status());
        }
        return $response->json();
    }

    function getAccessToken($timestamp, $clientId, $signature_auth)
    {
        $headers = [
            'X-TIMESTAMP' => $timestamp,
            'X-CLIENT-KEY' => $clientId,
            'X-SIGNATURE' => $signature_auth,
        ];

        $response = Http::withHeaders($headers)->post(
            'https://apidevportal.aspi-indonesia.or.id:44310/api/v1.0/access-token/b2b',
            [
                "grantType" => "client_credentials",
                "additionalInfo" => ""
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'error' => $response->json(),
            ], $response->status());
        }
        return $response->json()['accessToken'] ?? '';
    }

    function getSignService($timestamp, $clientSecret, $access_token)
    {
        $headers = [
            'accept' => 'application/json',
            'X-TIMESTAMP' => $timestamp,
            'X-CLIENT-SECRET' => $clientSecret,
            'HttpMethod' => 'POST',
            'EndpoinUrl' => '/api/v1.0/balance-inquiry',
            'AccessToken' => $access_token,
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->post(
            'https://apidevportal.aspi-indonesia.or.id:44310/api/v1.0/utilities/signature-service',
            [
                "partnerReferenceNo" => "2020102900000000000001",
                "bankCardToken" => "6d7963617264746f6b656e",
                "accountNo" => "2000100101",
                "balanceTypes" => [
                    "Cash",
                    "Coins"
                ],
                "additionalInfo" => [
                    "deviceId" => "12345679237",
                    "channel" => "mobilephone"
                ]
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'error' => $response->json(),
            ], $response->status());
        }
        return $response->json()['signature'] ?? '';
    }

    function getBalanceInfo($timestamp, $clientId, $access_token, $signature_service, $externalId, $channelId)
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature_service,
            'X-PARTNER-ID' => $clientId,
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID' => $channelId,
        ];

        // dd($signature_service);

        $response = Http::withHeaders($headers)->post(
            'https://apidevportal.aspi-indonesia.or.id:44310/api/v1.0/balance-inquiry',
            [
                "partnerReferenceNo" => "2020102900000000000001",
                "bankCardToken" => "6d7963617264746f6b656e",
                "accountNo" => "2000100101",
                "balanceTypes" => [
                    "Cash",
                    "Coins"
                ],
                "additionalInfo" => [
                    "deviceId" => "12345679237",
                    "channel" => "mobilephone"
                ]
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'error' => $response->json(),
            ], $response->status());
        }
        return $response->json();
    }
}
