<?php

namespace App\Integrations;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class WeatherApi implements \App\Contacts\WeatherApi
{

    /**
     * @throws Exception
     */
    protected function call(string $url, array $params = []): PromiseInterface|Response
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])
            ->get($url, $params);

        if ($response->status() !== 200) {
            logger()->channel('error')->error("Failed to call the url : $url", ['response' => $response->json()]);
            throw new Exception("Failed to call the url : $url");
        }

        logger()->channel('info')->info('Successfully called the url : ' . $url, ['response' => $response->json()]);
        return $response;
    }

}
