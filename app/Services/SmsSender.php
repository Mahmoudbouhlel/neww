<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsSender
{
    protected $apiKey = 'YtZJpkX6Sxnjk406/-/Ti2ihQVhIQFIJ4yIt6xJVz22x01A6JsQJhvvqnza9NfuyierAt8bc2UL8XFykhJ/I7lMcl/-/7YM9IRM4';

    public function send($toPhone, $message, $sender = 'LEONI')
    {
        $url = 'https://api.l2t.io/tn/v0/api/api.aspx';
        $response = Http::withoutVerifying()->get($url, [
            'fct'    => 'sms',
            'key'    => $this->apiKey,
            'mobile' => $toPhone,
            'sms'    => $message,
            'sender' => $sender,
            // 'date' => 'jj/mm/aaaa', // optional, can be omitted for instant send
            // 'heure' => 'hh:mm',     // optional
        ]);

        // Optionally, you can check $response->successful() or log the result.
        return $response->body();
    }
}
