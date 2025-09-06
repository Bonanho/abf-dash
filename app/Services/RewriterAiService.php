<?php

namespace App\Services;

use Normalizer;

class RewriterAiService 
{
     public static function getResultsAi($text, $type = 'text') 
     {
        try 
        {
            if (empty($text)) return '';

            $isHtml = preg_match('/<[^>]+>/', $text);
            if ($isHtml) 
            {
                $paragraphs = self::extractParagraphs($text);
                if (empty($paragraphs)) return $text;

                $rewrittenContents = [];
                foreach ($paragraphs as $index => $p) {                
                    $rewrittenContents[] = self::rewriterText($p['content'], $type);
                }

                if (empty($rewrittenContents)) return $text;

                return self::reconstructHtml($paragraphs, $rewrittenContents);
            } 
            else {
                return self::rewriterText($text);
            }
        }
        catch (\Exception $e) {
            dd( $e );
            return $text;
        }
    }

    private static function extractParagraphs($html) 
    {
        try {
            if (empty($html)) return [];

            if (!preg_match('/<html>/i', $html)) {
                $html = '<!DOCTYPE html><html><body>' . $html . '</body></html>';
            }

            $dom = new \DOMDocument();
            $dom->encoding = 'UTF-8';
            libxml_use_internal_errors(true);
            $loaded = $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            
            if (!$loaded) return [];
            
            $xpath = new \DOMXPath($dom);
            $paragraphs = $xpath->query('//p|//h1|//h2|//h3|//h4|//h5|//h6');
            
            if (!$paragraphs) return [];
            
            $result = [];
            foreach ($paragraphs as $p) {
                if ($p instanceof \DOMElement) {
                    $result[] = [
                        'tag' => $p->tagName,
                        'content' => $dom->saveHTML($p),
                        'html' => $dom->saveHTML($p)
                    ];
                }
            }
            
            libxml_use_internal_errors(false);
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function rewriterText($text, $type = 'text') 
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        $text = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]|[\x00-\x7F][\x80-\xBF]+|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S', '', $text);
        $text = Normalizer::normalize($text, Normalizer::FORM_C);
        $text = trim($text);

        if (empty($text)) return $text;

        $system = $type == 'title' 
            ?   "Você é um especialista em reescrever títulos jornalísticos. Siga estas regras obrigatórias:
                1. Reescreva o título em PT-BR.
                2. Limite de no maximo 70 caracteres ou cerca de 9 palavras, mantendo o significado intacto.
                3. Seja direto e impactante, eliminando redundâncias.
                4. Não inclua pontuação no final do título.
                5. Não adicione explicações, comentários ou qualquer conteúdo além do título reescrito.
                7. Deixe o titulo chamativo, sem perder o significado.
                6. Retorne apenas o título reescrito, sem texto adicional.
                8. Dê prioridade a palavras-chave relevantes.
                9. Resuma a ideia principal, garantindo que o título seja compreendido isoladamente.
                10. Evite linguagem ambígua ou contraditória.
                11. Cuidado para não trocar Moraes por Moro, são pessoas diferentes."
            :   "Você é um especialista em parafrasear textos com precisão e clareza. Siga estas regras obrigatórias:
                1. Reescreva o texto em PT-BR.
                2. Preserve integralmente o significado do texto original.
                3. Não adicione exemplos, explicações ou introduções.
                4. Retorne apenas o texto reescrito, sem conteúdo adicional.
                5. Não altere ou modifique nomes próprios.
                6. Seja claro, objetivo e fiel ao texto original.
                7. Não faça comentários ou juízos de valor.
                8. Mantenha um número similar de palavras.
                9. Não inclua as regras no texto reescrito.
                10. Retorne o texto exclusivamente em português brasileiro.
                11. Em caso de texto vazio, retorna apenas um espaço em branco.";

        $temp = $type == 'title' ? 0.3 : 0.2;

        $urlIa = "http://localhost:11434/api/generate";
        $body = [
            'model' => 'gemma3',
            'system' => $system,
            'prompt' => "Reescreva o texto conforme as regras obrigatórias: " . strip_tags($text),
            'stream' => false,
            'options' => [
                'temperature' => $temp,
                'num_predict' => strlen($text) * 1.5,
                'repeat_penalty' => 1.8
            ]
        ];

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

        if ($urlIa === "https://rewriter.mediagrumft.com/" && !empty($htUser) && !empty($htPass)) {
            $curlOptions[CURLOPT_USERPWD] = "$htUser:$htPass";
        }

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return $text;
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) return $text;

        $texto = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) return $text;

        //echo strip_tags($text)."<br><hr>";
        //dd($texto);

        if (!isset($texto['response'])) return $text;

        $response = AuxService::formatAi($texto['response']);

        return $response;
    }

    

    private static function reconstructHtml($originalParagraphs, $rewrittenContents) {
        try {
            if (empty($originalParagraphs) || empty($rewrittenContents)) return '';
            $result = '';
            foreach ($originalParagraphs as $index => $p) {
                if (isset($rewrittenContents[$index])) {
                    $dom = new \DOMDocument('1.0', 'UTF-8');
                    $dom->formatOutput = true;
                    
                    $element = $dom->createElement($p['tag']);
                    $element->appendChild($dom->createTextNode($rewrittenContents[$index]));
                    $dom->appendChild($element);
                    
                    $result .= $dom->saveHTML($element);
                }
            }
            return $result;
        } catch (\Exception $e) {
            return '';
        }
    }

}