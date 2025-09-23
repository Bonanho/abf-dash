<?php

namespace App\Services;

class SeoAiService 
{
     public static function optimizeSeo($title, $description, $content) 
     {
        try 
        {
            $keywords = self::generateKeywords($title, $description, $content);
            return [
                'keywords'      => $keywords,
                'focus_keyword' => $keywords[0] ?? '',
            ];
        } 
        catch (\Exception $e) {
            return [
                'keywords'      => [],
                'focus_keyword' => ''
            ];
        }
    }

    private static function generateKeywords($title, $description, $content) 
    {
        $prompt = "Analise o seguinte conteúdo e extraia as 5 principais palavras-chave para SEO. Retorne apenas as palavras-chave separadas por vírgula: \n\nTítulo: " . $title . "\nDescrição: " . $description . "\nConteúdo: " . $content;
        
        $keywords = self::callOpenAi($prompt, "Você é um especialista em SEO. Sua tarefa é identificar as palavras-chave mais relevantes para otimização e em PORTUGUÊS BRASILEIRO.");
        
        return array_map('trim', explode(',', $keywords));
    }

    private static function callOpenAi($prompt, $system) {

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
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.2,
            'max_tokens' =>  1.3
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
                'Authorization: Bearer '.env("AI_TOKEN").''
            ],
            CURLOPT_TIMEOUT => 300
        ];

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return '';
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) return '';

        $texto = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) return '';

        if (!isset($texto['response'])) return '';

        $response = $texto['response'];
        return $response;
    }
}