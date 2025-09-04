<?php

namespace App\Services;

use App\Models\SourceQueue;

class PostFetchService 
{
    public $path;
    public $gpushConfig;

    const FETCH_URL_PATTERN = "/wp-json/wp/v2/posts/";

    public static function fetchNewPost( $source )
    {
        try
        {
            $apiUrlBase = $source->url . self::FETCH_URL_PATTERN ;
            $apiUrl = $apiUrlBase . "?per_page=1" ;

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Connection: keep-alive',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    'DNT: 1',
                    'Upgrade-Insecure-Requests: 1',
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            $data = json_decode($response);

            return self::defineResult( $apiUrlBase, $data );
        }
         catch (\Throwable $e) {
            throw new \Exception("Erro ao buscar no source", 0, $e);
        }

    }

    public static function defineResult( $apiUrlBase, $data )
    {
        $resultData = (object) [];

        $resultData->id       = $data[0]->id;
        $resultData->endpoint = $apiUrlBase . $resultData->id;
        $resultData->data     = $data[0];

        $result[] = $resultData;

        return $result;
    }

    public static function getPostData( $sourceQueueId ) 
    {
        $sourceQueue = SourceQueue::find($sourceQueueId);
        // if( $sourceQueue->status_id != SourceQueue::STATUS_PENDING ){
        //     return false;
        // }

        $sourceQueue->setStatus( SourceQueue::STATUS_PROCESSING );

        try
        {
            $endpoint = $sourceQueue->endpoint;

            $ch = curl_init();
                curl_setopt_array($ch, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_ENCODING => 'gzip, deflate' // Suporte a compressão
            ]);
            
            $response = curl_exec($ch);

            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("cURL Error: {$error} | Endpoint: {$endpoint}");
            }
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                curl_close($ch);
                throw new \Exception("HTTP Error: {$httpCode} | Endpoint: {$endpoint}");
            }
            
            curl_close($ch);
            
            // Garantir que a resposta está em UTF-8
            if (!mb_check_encoding($response, 'UTF-8')) {
                $response = mb_convert_encoding($response, 'UTF-8', 'auto');
            }
            
            $decoded = json_decode($response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $jsonError = json_last_error_msg();
                throw new \Exception("JSON Error: {$jsonError} | Response: " . substr($response, 0, 500));
            }

            $sourceQueue->status_id = SourceQueue::STATUS_DONE;
            $sourceQueue->post_data = $decoded;
            $sourceQueue->save();
            
            return $decoded;
        }
        catch (\Throwable $e) 
        {
            $sourceQueue->setStatus( SourceQueue::STATUS_ERROR );
            $sourceQueue->postData = $e->getMessage(); // $e->serialize($e)
            $sourceQueue->save();

            throw new \Exception("Erro ao buscar no source [{$endpoint}]", 0, $e);
        }
    }



}