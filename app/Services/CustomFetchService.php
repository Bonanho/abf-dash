<?php

namespace App\Services;

use App\Models\SourcePost;
use DOMDocument;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CustomFetchService 
{
    public $source;
    public $baseUrl;
    public $result;

    public function __construct( $source )
    {
        $this->source = $source;
        $this->baseUrl = $source->url;
        $this->result = (object) [
            "sourceId"      => $source->id,
            "title"         => "",
            "content"       => "",
            "image"         => "",
            "url_original"  => "",
            "rewrited"      => "rewrited,",

            "image_caption" => "",
            "description"   => "",
            "category"      => 1,
            "post_id"       => 0,
        ];
    }
    
    public function returnAI( $text ) 
    {
        $contentPrompt = "
            Você é um Jornalista, especialista em reescrever textos para melhor indexação no Google News e SEO, reescreva o texto e Siga estas regras obrigatórias:
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
        ";

        if((int)$this->source->template->rewrite == 0){
            $contentPrompt = "
                1. Retorne o texto principal sem alterações, apenas removendo class, id e deixando o html mais limpo
                2. Remova menções a anuncios, leituras fora desse texto etc.
                3. Estrutura sugerida:
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
                4. NUNCA envolva o resultado em <html>, <head> ou <body>.
                5. NÃO escape os sinais de menor/maior; as tags devem ser reais, não literais.
            ";
        }

        $maxTokens = (int) (strlen($text) * 1.5);

        $urlIa = "https://api.openai.com/v1/chat/completions";
        $body = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $contentPrompt
                ],
                [
                    'role' => 'user',
                    'content' => "HTML da página:\n\n" . $text
                ]
            ],
            'temperature' => 0.2, // remover para o gtp-5
            'max_tokens' => $maxTokens
            //'max_completion_tokens' => $maxTokens // usar para o gtp-5
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
            return $text;
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            echo "Erro HTTP: " . $httpCode . "\n";
            echo "Resposta: " . $response . "\n";
            return $text;
        }

        $texto = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Erro JSON: " . json_last_error_msg() . "\n";
            echo "Resposta: " . $response . "\n";
            return $text;
        }
        if (empty(trim($texto['choices'][0]['message']['content']))) {
            echo "Resposta vazia da API\n";
            return $text;
        }
        
        return $texto['choices'][0]['message']['content'];
    }

    public function postExists( $sourceId, $url )
    {
        $exists = SourcePost::where("source_id",$sourceId)->where("endpoint",$url)->count();
        if( $exists ){
            echo "Matéria já existe\n";
        }
    }

    public function fetchSource( $crawler )
    {
        try {
            $imageUrl = $crawler->filterXPath('//meta[@property="og:image"]')->attr('content');    
            if ($this->testImageDownload($imageUrl)) {
                $this->result->image = $imageUrl;
                echo "Imagem válida: " . $imageUrl . "\n";
            } else {
                $this->result->image = "";
                echo "Imagem inválida ou inacessível, ignorando: " . $imageUrl . "\n";
            }
        } catch (\Exception $e) {
            $this->result->image = "";
            echo "Erro ao extrair imagem: " . $e->getMessage() . "\n";
        }

        try {
            $this->result->description = $crawler->filterXPath('//meta[@property="og:description"]')->attr('content');
        } catch (\Exception $e) {
            $this->result->description = "";
            echo "Erro ao extrair descrição: " . $e->getMessage() . "\n";
        }
        
        try {
            $this->result->title = $crawler->filter($this->source->template->title ?? 'h1')->first()->text();
        } catch (\Exception $e) {
            echo "Título não encontrado com seletor: " . ($this->source->template->title ?? 'h1') . "\n";
        }
        
        try {
            $container = $crawler->filter($this->source->template->content)->first();
            $this->result->content = $this->returnAI($container->html());
            $this->result->rewrited .= "content";
        } catch (\Exception $e) {
            echo "Conteúdo não encontrado com seletor: " . ($this->source->template->content) . "\n";
        }
        
        return $this->result;
    }

    public function testImageDownload( $imageUrl )
    {
        if (empty($imageUrl)) return false;
        try {
            $response = Http::timeout(10)->head($imageUrl);
            if ($response->ok()) {
                $contentType = $response->header('Content-Type');
                $contentLength = $response->header('Content-Length');
                if (strpos($contentType, 'image/') === 0 && 
                    $contentLength && 
                    $contentLength > 1000 && 
                    $contentLength < 10485760) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            echo "Erro ao testar imagem: " . $e->getMessage() . "\n";
            return false;
        }
    }

}