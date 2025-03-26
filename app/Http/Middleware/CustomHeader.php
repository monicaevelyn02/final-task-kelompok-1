<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AuthController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Custom headers
        $authHeaders = AuthController::Headers;
        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');
        $origin = $request->header('ORIGIN');
        $partnerId = $request->header('X-PARTNER-ID');
        $externalId = $request->header('X-EXTERNAL-ID');
        $channelId = $request->header('CHANNEL-ID');

        // Logic cek header
        if (
            $timestamp === $authHeaders['X-TIMESTAMP'] && $signature === $authHeaders['X-SIGNATURE'] &&
            $origin === $authHeaders['ORIGIN'] && $partnerId === $authHeaders['X-PARTNER-ID'] &&
            $externalId === $authHeaders['X-EXTERNAL-ID'] && $channelId === $authHeaders['CHANNEL-ID']
        ) {
            return $next($request);
        } else {
            return response()->json([
                'status' => 503,
                'message' => 'Silakan cek kembali header custom anda',
            ], 503);
        }

        return $next($request);
    }
}
