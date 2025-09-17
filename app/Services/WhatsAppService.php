<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function sendMessage(
        string $to,
        string $message,
        string $url          = null,
        string $filename     = null,
        int    $schedule     = 0,
        int    $delay        = 2,
        string $countryCode  = '62',
        string $buttonJSON   = null,
        string $templateJSON = null,
        string $listJSON     = null
    ) {
        // if (app()->runningInConsole()) {
        //     return [
        //         'status'  => 'skipped',
        //         'message' => 'Pesan tidak dikirim karena running in console',
        //     ];
        // }

        // Siapkan form data
        $multipart = [
            ['name' => 'target', 'contents' => $to],
            ['name' => 'message', 'contents' => $message],
            ['name' => 'schedule', 'contents' => $schedule],
            ['name' => 'delay', 'contents' => $delay],
            ['name' => 'countryCode', 'contents' => $countryCode],
        ];

        if ($url) {
            $multipart[] = ['name' => 'url', 'contents' => $url];
        }
        if ($filename) {
            $multipart[] = ['name' => 'filename', 'contents' => $filename];
        }
        if ($buttonJSON) {
            $multipart[] = ['name' => 'buttonJSON', 'contents' => $buttonJSON];
        }
        if ($templateJSON) {
            $multipart[] = ['name' => 'templateJSON', 'contents' => $templateJSON];
        }
        if ($listJSON) {
            $multipart[] = ['name' => 'listJSON', 'contents' => $listJSON];
        }

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.fonnte.com/send', [
            'headers' => [
                'Authorization' => env('FONNTE_ACCOUNT_TOKEN'),
            ],
            'multipart' => $multipart,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
