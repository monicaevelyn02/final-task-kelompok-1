<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    // Global variable for custom header
    // public const Headers = [
    //     'Accept' => 'application/json',
    //     'Content-Type' => 'application/json',
    //     'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiI3MTMwYjRkZC1kNzM1LTQxYWYtODI5Ny1lMmIwMGE3ZjM3ODQiLCJjbGllbnRJZCI6ImUwNGExMTA1MTk3MTRjYWJhY2ZjYjkxOWQzNWEwYjA3IiwibmJmIjoxNzQyOTk2NjY2LCJleHAiOjE3NDI5OTc1NjYsImlhdCI6MTc0Mjk5NjY2Nn0.jc4AkqE_U_MDsQfHm61lGtxASdNmw1cnC0Qf0Feg96U',
    //     'X-TIMESTAMP' => '2025-03-26T12:20:00+07:00',
    //     'X-SIGNATURE' => 'Y8a0K2qeGOTtWF5BgJ0lBWHu1wyjKry/zzuvMD11P/lrV3HObJCE3urcdcQ9XBVbbAnf3yV7ts7u2L2lW7Tv0A==',
    //     'X-PARTNER-ID' => 'e04a110519714cabacfcb919d35a0b07',
    //     'X-EXTERNAL-ID' => '41807553358950093184162180797837',
    //     'CHANNEL-ID' => '95221',
    // ];

    public function index()
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

    /**
     * @OA\Post(
     * path="/api/users/register",
     * operationId="Register",
     * tags={"Users"},
     * summary="User Register",
     * description="User Register here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name","email", "password", "password_confirmation"},
     *               @OA\Property(property="name", type="text"),
     *               @OA\Property(property="email", type="text"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="password_confirmation", type="password")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function register(Request $request)
    {
        $input = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Make user
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
        ]);

        // Make token
        $token = $user->createToken('DosimiliMana???')->plainTextToken;

        $data = [
            'status' => Response::HTTP_CREATED,
            'message' => 'User has been created.',
            'data' => $user,
            'token' => $token,
            'type' => 'Bearer',
        ];
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/users/login",
     *     operationId="Login",
     *     tags={"Users"},
     *     summary="User Login",
     *     description="User Login here",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="string", example="isan@gmail.com"),
     *               @OA\Property(property="password", type="string", example="123456"),
     *            ),
     *        ),
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="string", example="isan@gmail.com"),
     *               @OA\Property(property="password", type="string", example="123456"),
     *            ),
     *        ),
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="Login Successfully",
     *        @OA\JsonContent()
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Login Successfully",
     *        @OA\JsonContent()
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Unprocessable Entity",
     *        @OA\JsonContent()
     *    ),
     *    @OA\Response(response=400, description="Bad request"),
     *    @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function login(Request $request)
    {
        $input = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // User Authentication
        $user = User::where('email', $input['email'])->first();

        // User not found & wrong password logic
        if (!$user || !Hash::check($input['password'], $user->password)) {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('DosimiliMana???')->plainTextToken;

        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Login successful.',
            'data' => $user,
            'token' => $token,
            'type' => 'Bearer',
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     operationId="User",
     *     tags={"Users"},
     *     summary="User detail",
     *     description="Use Bearer Token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function user()
    {
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Detail user',
            'data' => auth()->user(),
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/users/logout",
     *     operationId="Logout",
     *     tags={"Users"},
     *     summary="User Logout",
     *     description="User Logout here",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout Successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logout()
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });

        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Logout successful.',
        ];

        return response()->json($data, Response::HTTP_OK);
    }
}
