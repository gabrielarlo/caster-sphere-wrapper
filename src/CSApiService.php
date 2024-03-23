<?php

namespace NineCloud\CasterSphereWrapper;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

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
     * Retrieves the list of rooms from the CS API.
     *
     * @return Response The HTTP response containing the list of rooms.
     */
    public function getRooms(): Response
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
     * @return Response The HTTP response from the API call.
     */
    public function createRoom(string $name): Response
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
     * Join to specific room.
     *
     * @param string $name description
     * @throws Response description of exception
     * @return Response
     */
    public function joinRoom(string $name): Response
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
     * A function to leave a room.
     *
     * @param string $name The name of the room to leave.
     * @return Response
     */
    public function leaveRoom(string $name): Response
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
     * Sends a message to a specified room.
     *
     * @param string $room The room to send the message to
     * @param string $encrypted_message The encrypted message to send
     * @param bool $persist (Optional) Whether to persist the message
     * @return Response The HTTP response from the server
     */
    public function sendMessage(string $room, string $encrypted_message, bool $persist = false): Response
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
