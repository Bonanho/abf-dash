<?php

namespace App\Services;

use GuzzleHttp\Client;

use \Exception;

class RequestWebService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function makeRequest($method, $url, $options = null)
    {
        try {
            $options = $options ?? [];
            if(env('APP_ENV')=="prod") {
                $response = $this->client->request($method, $url, $options);
                return json_decode( $response->getBody()->getContents() );
            }
            return true;
        }
        catch(Exception $error) {
            throw new Exception("Web Service Error: ".$error->getMessage());
        }
    }

    public function makeRequestGpush($method, $url, $options = null)
    {
        try {
            $options = $options ?? [];
            // if(env('APP_ENV')=="prod") {
                $response = $this->client->request($method, $url, $options);
                return json_decode( $response->getBody()->getContents() );
            // }
            return true;
        }
        catch(Exception $error) {
            echo $error->getMessage();
            return false;
        }
    }
}