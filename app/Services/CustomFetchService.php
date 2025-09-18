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

    ######################
    ### CUSTOM METHODS ###
    ######################
    
    public function returnAI( $text ) 
    {
        $maxTokens = (int) (strlen($text) * 1.5);

        $urlIa = "https://api.openai.com/v1/chat/completions";
        // $body = [
        //     'model' => 'gpt-4o-mini',
        //     'messages' => [
        //         [
        //             'role' => 'system',
        //             'content' => 'Você é um jornalista experiente especializado em SEO. Somente o texto formatado com paragrafos, titulos e subtitulos, remova classes e html desnecesarios, assim como referencias a publicidade.'
        //         ],
        //         [
        //             'role' => 'user',
        //             'content' => 'Reescreva e otimize a matéria com base no texto desse html, desconsiderando possiveis códigos fonte e javascript, retorne apenas o texto alterado: '.$text
        //         ]
        //     ],
        //     'temperature' => 0.2,
        //     'max_tokens' => $maxTokens
        // ];

        $body = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "Você é um jornalista e editor de texto, especialista em SEO.
                        Sua tarefa:
                        1. Identificar e extrair apenas o conteúdo principal da matéria jornalística a partir de um HTML completo.
                        2. Ignorar completamente qualquer código-fonte, JavaScript, menus, anúncios, rodapés ou outros elementos que não façam parte da notícia.
                        3. Reescrever e otimizar o texto para torná-lo claro e coeso, mantendo os fatos originais.
                        4. Retornar **somente o texto limpo**, formatado com títulos e parágrafos em português (pode usar Markdown, por exemplo '##' para títulos)."
                ],
                [
                    'role' => 'user',
                    'content' => "HTML da página:\n\n" . $text
                ]
            ],
            'temperature' => 0.2,
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
                'Authorization: Bearer '.env("AI_TOKEN").''
            ],
            CURLOPT_TIMEOUT => 300
        ];

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);

        //var_dump($response);
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

        //echo strip_tags($text)."<br><hr>";
        //dd($texto);

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
            throw new \Exception("Matéria já existe");
        }
    }

    public function definePostUrl( $url )
    {
        if (strpos($url, 'http') !== 0) {
            // $url = $this->baseUrl . $url;
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
            echo " ajusout-URL ";
        }
        return $url;
    }

    public function cleanHtml( $htmlString )
    {
        if (!preg_match('/<html>/i', $htmlString)) {
            $htmlString = '<!DOCTYPE html><html><body>' . $htmlString . '</body></html>';
        }

        $dom = new DOMDocument();

        // Carrega a string HTML. O '@' suprime avisos de HTML malformado.
        @$dom->loadHTML($htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Pega todos os elementos div
        $div_elements = $dom->getElementsByTagName('div');

        // Itera sobre cada elemento div
        foreach ($div_elements as $div) {
            // Lista de atributos a serem removidos
            $attributes_to_remove = ['class', 'id', 'style'];

            // Itera sobre a lista de atributos
            foreach ($attributes_to_remove as $attribute) {
                // Verifica se o atributo existe e o remove
                if ($div->hasAttribute($attribute)) {
                    $div->removeAttribute($attribute);
                }
            }
        }

        // Salva o HTML modificado
        $cleanHtml = $dom->saveHTML();

        return $cleanHtml;
    }

    ##########
    ## Estadão
    public function fetchSource_1( $crawler )
    {
        $this->result->title       = $crawler->filter('.container-news-informs h1')->first()->text();
        $this->result->image       = $crawler->filterXPath('//meta[@property="og:image"]')->attr('content');
        $this->result->description = $crawler->filterXPath('//meta[@property="og:description"]')->attr('content');

        $container = $crawler->filter('#content')->first()->html();

        $this->result->content   = $this->returnAI( $container );
        $this->result->rewrited .= "title,description,content,";

        return $this->result;
    }

    ##################
    ## Valor Economico
    public function fetchSource_2( $crawler )
    {
        $node = $crawler->filter('[class*="highlight"][class*="content"] a')->first();

        if ($node->count()) 
        {
            $link = $node->attr('href');
            $link = $this->definePostUrl($link);
        }

        $this->postExists( $this->source->id, $link );

        $this->result->url_original = $link;

        ### Pega dados da materia
        $detailResponse = Http::get($link);

        if ($detailResponse->ok()) 
        {
            $crawler = new Crawler($detailResponse->body(), $link);

            // Título
            $this->result->title = $crawler->filter('h1.content-head__title')->first()->text();
            $this->result->image = $crawler->filterXPath('//meta[@property="og:image"]')->attr('content');
            // dd($this->result);

            $container = $crawler->filter('div.no-paywall')->first()->html();

            $content = strip_tags($container);

            $this->result->content = $this->returnAI( $content );
            $this->result->rewrited.= "content,";

            return $this->result;
        }
    }
    
    #################
    ## Diario Oficial
    public function fetchSource_4( $crawler )
    {
        ## Pega materia nova
        $candidates = $crawler->filter('div.col-md-6.mb-3')->reduce(function (Crawler $node, $i) {
            try {
                return trim($node->filter('h2')->text()) === 'Destaque';
            } catch (\Exception $e) {
                return false;
            }
        });

        if (!$candidates->count()) {
            echo'Bloco "Destaque" não encontrado - verifique se o HTML mudou.';
            return 1;
        }

        $block = $candidates->first();

        if (!$block->filter('a.legenda')->count()) {
            echo'Link com classe "legenda" não encontrado dentro do bloco.';
            return 1;
        }

        $a = $block->filter('a.legenda')->first();
        $title = trim($a->text());

        $href  = trim($a->attr('href'));
        $href = $this->definePostUrl($href);

        $this->postExists( $this->source->id, $href );

        $this->result->url_original = $href;

        ### Pega dados da materia
        $detailResponse = Http::get($href);

        if ($detailResponse->ok()) {
            $crawler = new Crawler($detailResponse->body(), $href);

            // Título
            $this->result->title = $crawler->filter('h3.titulo')->first()->text();

            $container = $crawler->filter('div.col-md-12')->reduce(function ($node) {
                return $node->filter('h3.titulo')->count() > 0;
            })->first();

            // Imagem
            $this->result->image = $container->filter('img')->first()->attr('src');
            if (strpos($this->result->image, 'http') !== 0) {
                $this->result->image = rtrim('http://www.seguros.inf.br', '/') . '/' . ltrim($this->result->image, '/');
            }

            // Conteúdo
            $content = $container->filter('p.mb-3')->each(fn($p) => trim($p->text()));
            $this->result->content = implode("\n\n", $content);

            return $this->result;
        }
    }

    #####################
    ## Jornal do Comercio
    public function fetchSource_5( $crawler )
    {
        ## Pega materia nova
        $nodes = $crawler->filter('main a[href*="jornaldocomercio.com"], main a[href*="/economia/"]');

        if ($nodes->count()) {
            $href = $nodes->first()->attr('href');
            $href = $this->definePostUrl($href);
        }

        $this->postExists( $this->source->id, $href );

        $this->result->url_original = $href;
        
        ### Pega dados da materia
        $detailResponse = Http::get($href);

        if ($detailResponse->ok()) {
            $crawler = new Crawler($detailResponse->body(), $href);

            // Título
            $this->result->title = $crawler->filter('h1')->first()->text();

            // Imagem
            $this->result->image = $crawler->filter('div.relative.foto-principal img')->first()->attr('src');

            // Conteúdo            
            $container = $crawler->filter('div.noticia')->first();

            // remove nós indesejados (ads, paywall e "LEIA TAMBÉM")
            $container->filter('*')->each(function (Crawler $node) {
                $class = $node->attr('class') ?? '';
                $text  = trim($node->text());

                $isAdLike = preg_match('/adunit|ad-|advert|paywall|swiper|pagination|midias|intertext/i', $class);
                $isLeia  = stripos($text, 'LEIA TAMBÉM') !== false;

                if ($isAdLike || $isLeia) {
                    $dom = $node->getNode(0);
                    if ($dom && $dom->parentNode) {
                        $dom->parentNode->removeChild($dom);
                    }
                }
            });

            // coleta todos os parágrafos e divs com conteúdo relevante
            $parts = $container->filter('p, div')->each(function (Crawler $n) {
                $t = trim($n->text());
                if (!$t) return null;
                if (mb_strlen($t) < 30) return null; // ignora textos curtos
                if (preg_match('/^LEIA\b/i', $t)) return null; // ignora "LEIA TAMBÉM"
                return $t;
            });

            $parts = array_values(array_filter($parts));
            $this->result->content = implode("\n\n", $parts);

            return $this->result;
        }
        
    }

    #####################
    ## Exame - https://exame.com/
    public function fetchSource_6( $crawler )
    {
        ## Pega materia nova
        $nodes = $crawler->filter('#highlights > div.col-span-12.relative.grid.grid-cols-12.gap-3.px-0.lg\:px-0 > div:nth-child(2) > div > div:nth-child(1) > div > a');

        if ($nodes->count()) {
            $href = $nodes->first()->attr('href');
            $href = $this->definePostUrl($href);
        }

        $this->postExists( $this->source->id, $href );

        $this->result->url_original = $href;
        
        ### Pega dados da materia
        $detailResponse = Http::get($href);

        if ($detailResponse->ok()) {
            $crawler = new Crawler($detailResponse->body(), $href);

            $this->result->title = $crawler->filter('h1')->first()->text();
            $this->result->image = $crawler->filterXPath('//meta[@property="og:image"]')->attr('content');

            // Conteúdo            
            $container = $crawler->filter('#news-body')->first();

            // remove nós indesejados (ads, paywall e "LEIA TAMBÉM")
            $container->filter('#exm-see-also')->each(function (Crawler $node) {
                $dom = $node->getNode(0);
                if ($dom && $dom->parentNode) {
                    $dom->parentNode->removeChild($dom);
                }
            });

            // coleta todos os parágrafos e divs com conteúdo relevante
            $parts = $container->filter('p, div')->each(function (Crawler $n) {
                $t = trim($n->text());
                if (!$t) return null;
                if (mb_strlen($t) < 30) return null; // ignora textos curtos
                if (preg_match('/^LEIA\b/i', $t)) return null; // ignora "LEIA TAMBÉM"
                return $t;
            });

            $parts = array_values(array_filter($parts));
            $this->result->content = implode("\n\n", $parts);

            return $this->result;
        }
        
    }

}