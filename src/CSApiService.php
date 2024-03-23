<?php

namespace NineCloud\CasterSphereWrapper;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class CSApiService
{
    protected $csUrl;
    protected $csClientId;
    protected $csClientSecret;

    public function __construct()
    {
        $this->csUrl = config('cswrapper.url');
        $this->csClientId = config('cswrapper.client_id');
        $this->csClientSecret = config('cswrapper.client_secret');
    }

    /**
     * Retrieve a list of rooms from the specified URL using HTTP GET request.
     *
     * @return JsonResponse
     */
    public function getRooms(): JsonResponse
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->generateToken(),
            'app-id' => $this->csClientId,
        ])->get($this->csUrl . '/rooms');

        // Return the response as JSON
        return response()->json(json_decode($response->body()), $response->status());
    }

    /**
     * Creates a new room with the given name.
     *
     * @param string $name The name of the room to create.
     * @return JsonResponse The JSON response containing the result of the room creation.
     */
    public function createRoom(string $name): JsonResponse
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->generateToken(),
            'app-id' => $this->csClientId,
        ])->post($this->csUrl . '/create-room', [
            'name' => $name,
        ]);

        // Return the response as JSON
        return response()->json(json_decode($response->body()), $response->status());
    }

    /**
     * Joins a room with the given name.
     *
     * @param string $name The name of the room to join.
     * @return JsonResponse The JSON response containing the room information.
     */
    public function joinRoom(string $name): JsonResponse
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->generateToken(),
            'app-id' => $this->csClientId,
        ])->post($this->csUrl . '/join', [
            'name' => $name,
        ]);

        // Return the response as JSON
        return response()->json(json_decode($response->body()), $response->status());
    }

    /**
     * Leave a room by sending a POST request to the Caster Sphere API.
     *
     * @param string $name The name of the room to leave.
     * @return JsonResponse The JSON response from the API.
     */
    public function leaveRoom(string $name): JsonResponse
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->generateToken(),
            'app-id' => $this->csClientId,
        ])->post($this->csUrl . '/leave', [
            'name' => $name,
        ]);

        // Return the response as JSON
        return response()->json(json_decode($response->body()), $response->status());
    }

    /**
     * Sends a message to a room with optional persistence.
     *
     * @param string $room The room to send the message to
     * @param string $encrypted_message The encrypted message to be sent
     * @param bool $persist (Optional) Whether to persist the message
     * @return JsonResponse The response as JSON
     */
    public function sendMessage(string $room, string $encrypted_message, bool $persist = false): JsonResponse
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->generateToken(),
            'app-id' => $this->csClientId,
        ])->post($this->csUrl . '/message', [
            'room' => $room,
            'hashed_msg' => $encrypted_message,
            'persist' => $persist,
        ]);

        // Return the response as JSON
        return response()->json(json_decode($response->body()), $response->status());
    }

    private function generateToken(): string
    {
        $payload = [
            'platform' => 'web',
            'iss' => config('app.url'),
            'username' => auth()->check() ? auth()->user()->email : null,
            'sender_id' => auth()->check() ? auth()->id() : null,
        ];

        $token = JWT::encode($payload, $this->csClientSecret, 'HS256');

        return $token;
    }
}
