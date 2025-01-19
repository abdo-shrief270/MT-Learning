<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DailyService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('DAILY_API_KEY');
    }

    public function createMeeting($options = [])
    {
        $response = Http::withToken($this->apiKey)
            ->post('https://api.daily.co/v1/rooms', array_merge([
                'properties' => [
                    'enable_screenshare' => true,
                    'enable_chat' => true,
                ],
            ], $options));

        return $response->json();
    }

    public function getMeeting($meetingId)
    {
        $response = Http::withToken($this->apiKey)
            ->get("https://api.daily.co/v1/meetings/{$meetingId}");
        return $response->json();
    }
    public function getMeetings()
    {
        $response = Http::withToken($this->apiKey)
            ->get("https://api.daily.co/v1/meetings");
        return $response->json()['data'];
    }
    public function deleteRoom($roomName)
    {
        $response = Http::withToken($this->apiKey)
            ->delete("https://api.daily.co/v1/rooms/{$roomName}");
        return $response->json();
    }
}
