<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CustomFetchService 
{
    public $baseUrl;
    public $result;

    public function __construct( $source )
    {
        $this->baseUrl = $source->url;
        $this->result = (object) [
            "sourceId"      => $source->id,
            "title"         => "",
            "content"       => "",
            "image"         => "",
            "url_original"  => "",

            "image_caption" => "",
            "description"   => "",
            "category"      => 1,
            "post_id"       => 0,
        ];
    }

    ######################
    ### CUSTOM METHODS ###
    ######################
    
    ##################
    ## Valor Economico
    public function fetchSource_2( $crawler )
    {
        $node = $crawler->filter('.highlight__content a')->first();

        if ($node->count()) 
        {
            $titulo = trim($node->text());
            $link   = $node->attr('href');

            // Link pode ser relativo; garante URL absoluta
            if (strpos($link, 'http') !== 0) {
                $link = $this->baseUrl . $link;
            }
        }

        ### Pega dados da materia
        $detailResponse = Http::get($link);

        if ($detailResponse->ok()) 
        {
            $crawler = new Crawler($detailResponse->body(), $link);

            // Título
            $this->result->title = $crawler->filter('h1.content-head__title')->first()->text();
            $container = $crawler->filter('div.no-paywall');
            dd($this->result->title, $container);
            
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

        if (strpos($href, 'http') !== 0) {
            $href = rtrim($this->baseUrl, '/') . '/' . ltrim($href, '/');
        }

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
    public function fetchSource_14( $crawler )
    {
        ## Pega materia nova
        $nodes = $crawler->filter('main a[href*="jornaldocomercio.com"], main a[href*="/economia/"]');

        if ($nodes->count()) {
            $href = $nodes->first()->attr('href');

            // torna absoluto se for relativo
            if (strpos($href, 'http') !== 0) {
                $href = rtrim('http://jcrs.uol.com.br', '/') . '/' . ltrim($href, '/');
            }
        }

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

}