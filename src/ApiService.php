<?php

namespace NineCloud\CasterSphereWrapper;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ApiService
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

    public function getRooms(): Response
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . $this->generateToken(),
        ])->get($this->csUrl . '/rooms');
    }

    public function createRoom(string $name): Response
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . $this->generateToken(),
        ])->post($this->csUrl . '/create-room', [
            'name' => $name,
        ]);
    }

    public function joinRoom(string $name): Response
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . $this->generateToken(),
        ])->post($this->csUrl . '/join', [
            'name' => $name,
        ]);
    }

    public function leaveRoom(string $name): Response
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . $this->generateToken(),
        ])->post($this->csUrl . '/leave', [
            'name' => $name,
        ]);
    }

    public function sendMessage(string $name, string $encrypted_message, bool $persist = false): Response
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . $this->generateToken(),
        ])->post($this->csUrl . '/message', [
            'name' => $name,
            'hashed_msg' => $encrypted_message,
            'persist' => $persist,
        ]);
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
