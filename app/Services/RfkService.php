<?php

namespace App\Services;

use GuzzleHttp\Client;

class RfkService
{
    protected $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => env('API_RFK_URL'),
            'headers' => [
                'Content-Type' => "application/json",
            ]
        ]);
    }

    public function consumeGET($url, $params = [])
    {
        try {
            $response = $this->http->get($url, ['query' => $params, 'headers' => ['Authorization' => "Bearer " . session('token')]]);
            $response = json_decode($response->getBody()->getContents(), true);
            return $response;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
    }
}
