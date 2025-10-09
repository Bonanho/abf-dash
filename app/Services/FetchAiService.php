<?php

namespace App\Services;

use Normalizer;

class FetchAiService 
{
    public static function fetchSource( $source ) 
     {
        try 
        {
            $url = "https://www.estadao.com.br";//$source->url;

            $result = self::fetchAI( $url );
            dd( $result );
            return $result;
        }
        catch (\Exception $e) {
            dd( $e );
            return $text;
        }
    }

    public static function fetchAI( $url ) 
    {
        $website = "https://www.estadao.com.br"; // site fixo
        $periodo = "hoje";
        // $periodo = "este ano";
        $tema = "musica";
        // $subtemas = "
        //     - seguro de vida
        //     - seguro residencial
        //     - seguro de automóvel
        //     - mercado de seguros"
        // ;

        $system = "Você é um assistente que busca notícias recentes no site $website.
            Sua tarefa é retornar **apenas um link** da **última matéria publicada $periodo** que fale sobre **$tema**, incluindo:
                - Arnaldo antunes

            Regras importantes:
            1. A matéria deve ter sido publicada **$periodo**.
            2. Somente retornar **o link direto da matéria**.
            3. Se não houver nenhuma matéria sobre o tema, responda apenas **NULL**.
            4. Não adicionar comentários, descrições ou resumos. Somente o link ou NULL.

            Exemplo de saída esperada:
            - \"https://www.estadao.com.br/exemplo-da-materia\" 
            ou
            - NULL"
        ;

        $maxTokens = 200;
        $temp = 0.0;
        $urlIa = "https://api.openai.com/v1/chat/completions";

        $body = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $system
                ],
                [
                    'role' => 'user',
                    'content' => "Busque uma matéria publicada $periodo sobre $tema no site $website"
                ]
            ],
            'temperature' => $temp,
            'max_tokens' => $maxTokens
        ];

        $ch = curl_init();
        $curlOptions = [
            CURLOPT_URL => $urlIa,
            CURLOPT_POST => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.env("AI_TOKEN")
            ],
            CURLOPT_TIMEOUT => 300
        ];

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return "";
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) return "";

        $texto = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) return "";

        if (!isset($texto['choices'][0]['message']['content'])) return "";
        $response = $texto['choices'][0]['message']['content'];

        return $response;
    }

}