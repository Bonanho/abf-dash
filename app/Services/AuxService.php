<?php

namespace App\Services;

use Normalizer;

class AuxService 
{
    public static function formatAi($response) 
    {
         // Ensure proper UTF-8 encoding
         if (!mb_check_encoding($response, 'UTF-8')) {
            $response = mb_convert_encoding($response, 'UTF-8', 'auto');
        }

        // Normalize the text to NFC form
        $response = Normalizer::normalize($response, Normalizer::FORM_C);

        // Fix common encoding issues
        $response = str_replace(
            ['Ã§', 'Ã£', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã¢', 'Ãª', 'Ã´', 'Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº'],
            ['ç', 'ã', 'é', 'í', 'ó', 'ú', 'â', 'ê', 'ô', 'á', 'é', 'í', 'ó', 'ú'],
            $response
        );

        // Remove caracteres inválidos
        $response = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]|[\x00-\x7F][\x80-\xBF]+|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S', '', $response);
        $response = preg_replace('/[\x00-\x1F\x7F]/u', '', $response);
        
        // Remove notas de tradução e frases introdutórias
        $response = preg_replace('/\(Note:.*?\)/i', '', $response);
        $response = preg_replace('/\(Note:.*?\)/i', '', $response);
        $response = preg_replace('/\(Tradução.*?\)/i', '', $response);
        $response = preg_replace('/\(Nota:.*?\)/i', '', $response);
        $response = preg_replace('/\(Observação:.*?\)/i', '', $response);
        $response = preg_replace('/\(Comentário:.*?\)/i', '', $response);
        $response = preg_replace('/\(Texto.*?\)/i', '', $response);
        $response = preg_replace('/\(Original.*?\)/i', '', $response);
        $response = preg_replace('/\(O original diz.*?\)/i', '', $response);
        $response = preg_replace('/Note que.*?:/i', '', $response);
        $response = preg_replace('/Note-se que.*?:/i', '', $response);
        $response = preg_replace('/Vale notar que.*?:/i', '', $response);
        $response = preg_replace('/É importante notar que.*?:/i', '', $response);
        $response = preg_replace('/Cabe destacar que.*?:/i', '', $response);
        $response = preg_replace('/Vale destacar que.*?:/i', '', $response);
        $response = preg_replace('/É importante destacar que.*?:/i', '', $response);
        $response = preg_replace('/O texto original é.*?:/i', '', $response);
        $response = preg_replace('/Nenhum texto encontrado.*?:/i', '', $response);
        $response = preg_replace('/A página está vazia.*?:/i', '', $response);
        $response = preg_replace('/Texto original.*?:/i', '', $response);
        $response = preg_replace('/Não há nenhum texto para re.*?:/i', '', $response);
        $response = preg_replace('/Não existe texto para re.*?:/i', '', $response);
        $response = preg_replace('/Não há conteúdo para re.*?:/i', '', $response);
        $response = preg_replace('/Não existe conteúdo para re.*?:/i', '', $response);
        $response = preg_replace('/Não há texto*?:/i', '', $response);
        $response = preg_replace('/Não há nada a ser reescrito.*?:/i', '', $response);
        $response = preg_replace('/Não há nada para reescrever.*?:/i', '', $response);
        $response = preg_replace('/Não existe nada para reescrever.*?:/i', '', $response);
        $response = preg_replace('/Não há conteúdo para reescrever.*?:/i', '', $response);
        $response = preg_replace('/Não existe conteúdo para reescrever.*?:/i', '', $response);
        $response = preg_replace('/O texto não pode ser reescrito.*?:/i', '', $response);
        $response = preg_replace('/O conteúdo não pode ser reescrito.*?:/i', '', $response);
        $response = preg_replace('/O texto diz.*?:/i', '', $response);
        $response = preg_replace('/Segue o texto.*?:/i', '', $response);
        $response = preg_replace('/Aqui está o texto.*?:/i', '', $response);
        $response = preg_replace('/O conteúdo é.*?:/i', '', $response);
        $response = preg_replace('/O artigo.*?:/i', '', $response);
        $response = preg_replace('/A matéria.*?:/i', '', $response);
        $response = preg_replace('/O conteúdo original diz.*?:/i', '', $response);
        $response = preg_replace('/O conteúdo do parágrafo.*?:/i', '', $response);
        $response = preg_replace('/O parágrafo original.*?:/i', '', $response);
        $response = preg_replace('/O texto do parágrafo.*?:/i', '', $response);
        $response = preg_replace('/O conteúdo menciona.*?:/i', '', $response);
        $response = preg_replace('/O texto menciona.*?:/i', '', $response);
        $response = preg_replace('/O parágrafo menciona.*?:/i', '', $response);
        $response = preg_replace('/Análise do Texto:*?:/i', '', $response);
        $response = preg_replace('/Não há necessidade de alteração.*?:/i', '', $response);
        $response = preg_replace('/Não é necessário fazer alterações.*?:/i', '', $response);
        $response = preg_replace('/O texto não precisa de alterações.*?:/i', '', $response);
        $response = preg_replace('/Não há necessidade de modificação.*?:/i', '', $response);
        $response = preg_replace('/Não é necessário modificar.*?:/i', '', $response);
        $response = preg_replace('/O texto não precisa de modificação.*?:/i', '', $response);
        $response = preg_replace('/Não há necessidade de mudança.*?:/i', '', $response);
        $response = preg_replace('/Não é necessário mudar.*?:/i', '', $response);
        $response = preg_replace('/O texto não precisa de mudança.*?:/i', '', $response);
        $response = preg_replace('/Não há necessidade de alteração no con.*?:/i', '', $response);
        $response = preg_replace('/Faça parte do grupo de Astrologia do Personare no WhatsApp.*?:/i', '', $response);

        // Remove expressões explicativas indesejadas
        //$response = preg_replace('/\b(?:ou seja|isto é|quer dizer|em outras palavras|por outras palavras|em resumo|resumindo|em suma|portanto|assim sendo|sendo assim|desta forma|deste modo|logo|consequentemente|por conseguinte)[\s,:]*/i', '', $response);
        
        // Remove tags HTML específicas
        /*$response = preg_replace('/<h2\b[^>]*>(.*?)<\/h2>/is', '', $response);
        $response = preg_replace('/<h3\b[^>]*>(.*?)<\/h3>/is', '', $response);
        $response = preg_replace('/<strong\b[^>]*>(.*?)<\/strong>/is', '', $response);
        $response = preg_replace('/<h4\b[^>]*>(.*?)<\/h4>/is', '', $response);*/
        
        // Remover atributos de id com aspas retas e curvas (e.g., id="...", id='...', id=”...”, id=’...’)
        $response = preg_replace('/\s+id\s*=\s*"[^"]*"/u', '', $response);
        $response = preg_replace("/\s+id\s*=\s*'[^']*'/u", '', $response);
        $response = preg_replace('/\s+id\s*=\s*[“][^”]*[”]/u', '', $response);
        $response = preg_replace('/\s+id\s*=\s*[‘][^’]*[’]/u', '', $response);
        $response = preg_replace('/\s+align\s*=\s*"[^"]*"/u', '', $response);
        $response = preg_replace("/\s+align\s*=\s*'[^']*'/u", '', $response);
        $response = preg_replace('/\s+align\s*=\s*[“][^”]*[”]/u', '', $response);
        $response = preg_replace('/\s+align\s*=\s*[‘][^’]*[’]/u', '', $response);

        // Remove marcadores Markdown e outros
        $response = str_replace('Saiba mais no Poder360.', "", $response); 
        $response = str_replace('Saiba mais no Poder360', "", $response); 
        $response = str_replace('“`html', "", $response); 
        $response = str_replace('<p>&gt;', "<p>", $response); 
        $response = str_replace('<p>>', "<p>", $response); 
        //$response = str_replace('<h2 ', "", $response); 
        $response = str_replace('```html', "", $response); 
        $response = str_replace('```', "", $response); 
        $response = str_replace('<li ></li>', "", $response); 
        $response = str_replace('<p></p>', "", $response); 
        $response = str_replace('&lt;p&gt;&lt;/p&gt;', "", $response); 
        $response = str_replace('<ul><li></li>', "", $response); 
        $response = str_replace('<li></li>', "", $response); 
        $response = str_replace('<li></li></ul>', "", $response); 
        $response = str_replace('“`', "", $response); 
        $response = str_replace('h3:', "", $response); 
        $response = str_replace('h2:', "", $response); 
        $response = str_replace('align=”center”>', "", $response); 
        $response = str_replace('align=”CENTER”', "", $response); 
        $response = str_replace('align="center"', "", $response); 
        $response = str_replace('**', "", $response); 
        $response = str_replace('&gt;', ">", $response); 
        $response = str_replace('&lt;', "<", $response); 
        $response = str_replace('Observe as imagens.', "<", $response); 
        $response = str_replace('Notícias relacionadas:', '', $response);
        $response = str_replace('Leia também:', '', $response);
        $response = str_replace('Por favor, forneça o texto que você deseja', '', $response);
        $response = str_replace('Por favor, forneça o texto original que você deseja que eu reescreva', '', $response);
        $response = str_replace('Reescreva o texto em PT-BR de', '', $response);
        $response = str_replace('Não há texto para reescrever.', '', $response);
        $response = str_replace('Assista ao vídeo:', '', $response);
        $response = str_replace('O texto está vazio.', '', $response);
        $response = str_replace('Leia mais', '', $response);
        $response = str_replace('Não há texto disponível.', '', $response);
        $response = str_replace('Não há conteúdo disponível.', '', $response);
        $response = str_replace('Escreva o texto em PT-BR de forma', '', $response);
        $response = str_replace('Veja vídeo:', '', $response);
        $response = str_replace('Veja o vídeo:', '', $response);
        $response = str_replace('Veja:', '', $response);
        $response = str_replace('Assista:', '', $response);
        $response = str_replace('Veja vídeo', '', $response);
        $response = str_replace('Veja vídeo:', '', $response);
        $response = str_replace('Assista ao vídeo.', '', $response);
        $response = str_replace('Assista ao vídeo', '', $response);
        $response = str_replace('Sob vigilância', '', $response);
        $response = str_replace('Vídeo:', '', $response);
        $response = str_replace('C&amp;M', 'C&M', $response);
        $response = str_replace('Observe a cena:', '', $response);

        // Limpa espaços múltiplos e quebras de linha
        $response = preg_replace('/\s+/', ' ', $response);
        $response = trim($response);
        
        $response = Normalizer::normalize($response, Normalizer::FORM_C);
                
        return $response;
    }

    public static function validText($text) 
    {
        $system = "Você é um especialista em analise de textos jornalisticos com precisão e clareza. Siga estas regras obrigatórias:
                1. Analise o texto e verifique se ele está em português brasileiro.
                2. Preserve integralmente o significado do texto original.
                3. Não adicione exemplos, explicações ou introduções.
                4. Remova coisas sem sentido, como referencias a coisas que não existem no texto (imagens, videos, tabelas etc).
                5. Não altere ou modifique nomes próprios.
                6. Mantenha o código html do texto.
                7. Caso o texto não tenha estrutura html de parágrafo, subtitulo, lista adicione para melhorar a leitura (não usar h1).
                8. Remova menção a elementos que não existe no código como exemplo videos.
                9. Se necessario, melhore e aumente o texto deixando pronto para SEO.";

        $urlIa = 'http://localhost:11434/api/generate';
        $body = [
            'model' => 'gemma3',
            'system' => $system,
            'prompt' => "Analise o texto conforme as regras obrigatórias: " . $text,
            'stream' => false,
            'options' => [
                'temperature' => 0.1,
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

        if ($urlIa === 'https://rewriter.mediagrumft.com/' && !empty($htUser) && !empty($htPass)) {
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

        if (!isset($texto['response'])) return $text;

        $response = self::FormatAi($texto['response']);
        $response = preg_replace('/<h1\b[^>]*>(.*?)<\/h1>/is', '<h2>$1</h2>', $response);

        return $response;
    }

    /**
     * Remove blocos HTML repetidos (p, h1-h6, li, blockquote, pre) mantendo a primeira ocorrência.
     * Útil para eliminar repetições geradas pela origem ou pela IA.
     */
    public static function removeRepeatedBlocks($content) {
        if (empty($content)) return $content;

        // Divide o conteúdo em blocos começando em tags de bloco comuns
        $blocks = preg_split('/(?=<\/(?:p|h[1-6]|li|blockquote|pre)\b)|(?=<(?:p|h[1-6]|li|blockquote|pre)\b)/iu', $content, -1, PREG_SPLIT_NO_EMPTY);
        if ($blocks === false || count($blocks) === 0) return $content;

        $seen = [];
        $deduped = [];
        $lastNorm = '';

        foreach ($blocks as $block) {
            $innerText = trim(strip_tags($block));
            $normalized = mb_strtolower($innerText, 'UTF-8');
            $normalized = preg_replace('/\s+/u', ' ', $normalized);
            $normalized = trim($normalized);

            // Ignorar blocos muito curtos ao deduplicar (ex.: títulos de uma palavra como "Leão") somente se consecutivos
            $isShort = (mb_strlen($normalized, 'UTF-8') < 5);

            if ($normalized !== '' && (isset($seen[$normalized]) || (!$isShort && $normalized === $lastNorm))) {
                // bloco repetido → pular
                continue;
            }

            $deduped[] = $block;
            $seen[$normalized] = true;
            $lastNorm = $normalized;
        }

        $joined = implode('', $deduped);
        return $joined ?: $content;
    }
    
}