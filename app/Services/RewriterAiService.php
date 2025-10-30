<?php

namespace App\Services;

use Normalizer;

class RewriterAiService 
{
    public static function getResultsAi($text, $type = 'text', $shouldRewrite = true) 
     {
        try 
        {
            if (empty($text)) {
                return self::errorResponse("Variável text chegou vazia!");
            }

            $result = self::rewriterText($text, $type, $shouldRewrite);

            return $result;
        }
        catch (\Exception $e) {
            //dd( $e );
            return self::errorResponse("Erro inesperado no processo");
        }
    }

    public static function rewriterText($text, $type = 'text', $shouldRewrite = true) 
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        $text = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]|[\x00-\x7F][\x80-\xBF]+|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S', '', $text);
        $text = Normalizer::normalize($text, Normalizer::FORM_C);
        $text = trim($text);

        if (empty($text)) return $text;

        if (!$shouldRewrite) 
        {
            if ($type == 'text') 
            {
                $promptSystem = " REGRA FUNDAMENTAL:
                - NUNCA, JAMAIS, EM HIPÓTESE ALGUMA altere o texto ou palavras do texto.
                - Mantenha EXATAMENTE o texto como está no original

                REGRAS OBRIGATÓRIAS PARA LIMPEZA HTML:
                    1. Retorne APENAS o texto principal com tags HTML puras de formatação
                    2. REMOVA COMPLETAMENTE todas as tags estruturais: <div>, <span>, <section>, <article>, <header>, <footer>, <nav>, <aside>, html, head, body, etc.
                    3. REMOVA todos os atributos: class, id, style, data-*, onclick, onload, etc.
                    4. MANTENHA APENAS estas tags de formatação: <p>, <h1>, <h2>, <h3>, <h4>, <h5>, <h6>, <strong>, <b>, <em>, <i>, <u>, <br>, <ul>, <ol>, <li>, <a>
                    5. Para links <a>, mantenha os atributos href, target, rel.
                    6. Remova menções a anuncios, leituras fora desse texto (como leia também, leia mais, notícias relacionadas, conteúdo relacionado, etc)
                    7. NUNCA envolva o resultado em <html>, <head> ou <body>
                    8. NÃO escape os sinais de menor/maior; as tags devem ser reais, não literais
                    9. Se não houver conteúdo textual relevante, retorne apenas o texto sem tags
                ";

                $temp = 0;
            }
        }
        elseif ( $shouldRewrite && $type == 'text' ) 
        {
            $promptSystem = "Você é um Jornalista, especialista em reescrever textos para melhor indexação no Google News e SEO, reescreva o texto e Siga estas regras obrigatórias:
                1. Identificar e extrair apenas o conteúdo principal da matéria jornalística a partir de um HTML completo.
                2. Ignorar completamente qualquer código-fonte, JavaScript, menus, anúncios, rodapés ou outros elementos que não façam parte da notícia.
                3. Reescrever e otimizar o texto para torná-lo claro e coeso, mantendo os fatos originais.
                4. Estruture o HTML APENAS com estas tags: <h2>, <h3>, <p>, <ul>, <li>.
                5. Não use <h1>, <h4+>, <table>, <blockquote>, <code> ou outras tags.
                6. Estrutura sugerida:
                    <h2>Titulo Relevante (opcional)</h2>
                    <p>...</p>
                    <h2>Subtítulo 1 relevante ao tema</h2>
                    <p>...</p>
                    <h3>Subseção relevante (opcional)</h3>
                    <p>...</p>
                    <h3>Outra subseção (opcional)</h3>
                    <p>...</p>
                    <h2>Subtítulo para Conclusão</h2>
                    <p>...</p>
                7. Parágrafos: escreva de 2 a 4 parágrafos por seção, com 2 a 4 frases cada.
                8. Listas: use <ul><li>...</li></ul> somente quando o conteúdo exigir enumeração clara.
                9. NUNCA envolva o resultado em <html>, <head> ou <body>.
                10. NÃO escape os sinais de menor/maior; as tags devem ser reais, não literais.
                11. Retorne em HTML válido usando apenas <h2>, <h3>, <p>, <ul>, <li>
                12. Remova menções a anuncios, leituras fora desse texto etc.
                13. Remova citações a outros sites de notícias como CNN, O Antagonista, Exame etc.
                14. Reescreva o texto em PT-BR.
            ";

            $temp = 0.2;
        } 
        else 
        {
            $RwType = 'textos';
            if ($type == 'title') {
                $RwType = 'títulos';
                $RwMin = 60;
                $RwMax = 95;
            } else if ($type == 'description') {
                $RwType = 'descrições';
                $RwMin = 120;
                $RwMax = 160;
            }
           
            $RwMinMax = "Preserve integralmente o significado do texto original";
            if($type == 'title' || $type == 'description') {
                $RwMinMax = "Mínimo de ".$RwMin." caracteres e máximo de ".$RwMax.", mantendo o significado intacto.";
            }

            $promptSystem = "Você é um Jornalista, especialista em reescrever ".$RwType." para Google News. Siga estas regras obrigatórias:
                REGRA FUNDAMENTAL: PRESERVAÇÃO ABSOLUTA DE NOMES PRÓPRIOS
                - NUNCA, JAMAIS, EM HIPÓTESE ALGUMA altere ou substitua nomes próprios
                - Mantenha os nomes EXATAMENTE como está no texto original
                - Se não é citado nome, não cite também

                REGRAS OBRIGATÓRIAS:
                1. Reescreva a descrição em PT-BR.
                2. ".$RwMinMax."
                3. Seja direto e impactante.
                4. NUNCA altere nomes, datas, locais e acontecimentos do texto enviado.
                5. Não adicione explicações, comentários ou qualquer conteúdo além do título reescrito.
                6. Retorne apenas o título reescrito, sem texto adicional.
                7. Deixe o título chamativo, sem perder o significado.
                8. Dê prioridade a palavras-chave relevantes.
                9. Garantindo que o título seja compreendido isoladamente.
                10. Evite linguagem ambígua ou contraditória.
                
                EXEMPLOS DE PRESERVAÇÃO DE NOMES:
                - 'Trump anuncia nova política' → 'Trump revela política inovadora'
                - 'Moraes decide sobre caso' → 'Moraes define posição no processo'
                - 'Vini Jr marca gol' → 'Vini Jr faz gol decisivo'
                - 'Trump encontra Elon Musk' → 'Trump e Elon Musk são vistos juntos'

                ATENÇÃO CRÍTICA: Se você trocar qualquer nome próprio, a resposta estará INCORRETA.
            ";

            $temp = 0.3;
        }

        $numText = strlen($text);
        $maxTokens = (int) ceil($numText * 1.5);
        
        $maxContextTokens = 15000; //"message": "max_tokens is too large: 22160. This model supports at most 16384 completion tokens, whereas you provided 22160."
        if ($numText > $maxContextTokens) {
            echo "Matéria ignorada: excede limite de tokens do GPT-4o-mini (" . number_format($numText) . " caracteres > " . number_format($maxContextTokens) . " tokens)\n";

            return self::errorResponse( "Matéria excede limite de tokens do GPT-4o-mini com ".number_format($numText) );
        }

        $urlIa = "https://api.openai.com/v1/chat/completions";
        $body = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $promptSystem
                ],
                [
                    'role' => 'user',
                    'content' => "HTML da página:\n\n" . $text
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
            return self::errorResponse("Erro na requisição para AI");
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return self::errorResponse(json_decode($response)->error->message);
        }

        $texto = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return self::errorResponse("Erro ao decodificar JSON retornado pela AI");
        }

        if (!isset($texto['choices'][0]['message']['content'])) {
            return self::errorResponse("Erro ao decodificar receber texto de IA. Padrão (texto['choices'][0]['message']['content'])");
        }

        $response = $texto['choices'][0]['message']['content'];

        return $response;
    }

    public static function errorResponse( $error ) 
    {
        $response = (object) [];
        $response->type = "Error";
        $response->message = $error;

        return $response;
    }
}