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
                'title'         => $title,
                'description'   => $description,
                'keywords'      => $keywords,
                'focus_keyword' => $keywords[0] ?? '',
            ];
        } 
        catch (\Exception $e) {
            return [
                'title'         => $title,
                'description'   => $description,
                'keywords'      => [],
                'focus_keyword' => ''
            ];
        }
    }

    private static function generateKeywords($title, $description, $content) 
    {
        $prompt = "Analise o seguinte conteúdo e extraia as 5 principais palavras-chave para SEO. Retorne apenas as palavras-chave separadas por vírgula, sem números, sem pontos, sem parênteses, sem aspas:\n\nTítulo: " . $title . "\nDescrição: " . $description . "\nConteúdo: " . $content;
        
        $keywords = self::callMistralApi($prompt, "Você é um especialista em SEO. Sua tarefa é identificar as palavras-chave mais relevantes para otimização e em PORTUGUÊS BRASILEIRO.");
        
        return array_map('trim', explode(',', $keywords));
    }

    private static function optimizeTitle($title) {
        $prompt = "Otimize o seguinte título para SEO, faça ficar mais chamativo e atraente:\n\n" . $title;
        
        return self::callMistralApi($prompt, "Você é um especialista em SEO. Sua tarefa é otimizar títulos para melhorar o ranqueamento nos motores de busca, mantendo a naturalidade e atratividade e em PORTUGUÊS BRASILEIRO.");
    }

    private static function optimizeDescription($description) {
        $prompt = "Otimize a seguinte descrição para SEO, mantendo-a informativa e com palavras-chave relevantes. Retorne apenas a descrição otimizada:\n\n" . $description;
        
        return self::callMistralApi($prompt, "Você é um especialista em SEO. Sua tarefa é otimizar meta descrições para melhorar o CTR nos resultados de busca, mantendo a clareza e relevância e em PORTUGUÊS BRASILEIRO.");
    }

    

    private static function callMistralApi($prompt, $system) {

        // if(getenv('URL') == "http://rewriterai.test"){
            $urlIa = 'http://localhost:11434/api/generate';
            $body = array(
                'model' => 'gemma3',
                'system' => $system,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.2,
                    'num_predict' => 500,
                    'repeat_penalty' => 1.3
                ]
            );
        // } else {
        //     $urlIa = 'https://rewriter.mediagrumft.com/';
        //     $body = [
        //         'system' => $system,
        //         'temperature' => 0.2,
        //         'prompt' => $prompt,
        //     ];
        //     $htUser = 'grumft';
        //     $htPass = 'grumft';
        // }

        $ch = curl_init();
        $curlOptions = [
            CURLOPT_URL => $urlIa,
            CURLOPT_POST => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=utf-8',
                'Accept-Charset: utf-8'
            ],
            CURLOPT_TIMEOUT => 300
        ];

        if ($urlIa === 'https://rewriter.mediagrumft.com/' && !empty($htUser) && !empty($htPass)) {
            $curlOptions[CURLOPT_USERPWD] = "$htUser:$htPass";
        }

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
        if (!mb_check_encoding($response, 'UTF-8')) {
            $response = mb_convert_encoding($response, 'UTF-8', 'auto');
        }

        $response = AuxService::FormatAi($response);

        $patterns = [ '- ', '1)-', '2)-', '3)-', '4)-', '5)-', '"', '(', ')', '1)', '2)', '3)', '4)', '5)', '1.', '2.', '3.', '4.', '5.', '1 ', '2 ', '3 ', '4 ', '5 ' ];
        $response = str_replace($patterns, '', $response);

        return $response;
    }
}