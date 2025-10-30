<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

use App\Models\Source;
use App\Models\SourcePost;

class PostFetchService 
{
    public $source;
    public $sourcePost;
    public $apiUrlBase;
    public $apiUrlBasePost;
    public $apiUrlBaseMedia;
    public $apiUrlBaseCategory;

    public function __construct( $source )
    {
        $this->source = $source;

        $baseUrl = ( @$source->template->wpEndpoint ) ? $source->template->wpEndpoint : $this->source->url;

        $this->apiUrlBase         = $baseUrl . "/wp-json/wp/v2";
        $this->apiUrlBasePost     = $this->apiUrlBase . "/posts/";
        $this->apiUrlBaseMedia    = $this->apiUrlBase . "/media/";
        $this->apiUrlBaseCategory = $this->apiUrlBase . "/categories/";
    }

    public function fetchValidation()
    {
        $apiUrl = $this->apiUrlBasePost . "?per_page=1" ;

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

        if( is_array($data) && isset($data[0]) && is_object($data[0]) && $data[0]->id  ) 
        {
            $this->source->status_id = Source::STATUS_ACTIVE;
            $this->source->save();
        }
        else
        {
            $this->source->status_id = Source::STATUS_INVALID;
            // $this->source->type_id   = Source::TYPE_CUSTOM;
            $this->source->doc       = "{'test-url':$apiUrl,'result':$response}";
            $this->source->save();

            echo "\n".$apiUrl."\n";
        }
        
        return $this->source->status_id;
    }

    public function fetchNewPost()
    {
        try
        {
            if ($this->source->type_id === Source::TYPE_WP) 
            {
                $apiUrl = $this->apiUrlBasePost . "?per_page=1" ;

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
                if (stripos($response, 'recaptcha') !== false) {
                    throw new \Exception("Erro ao acessar, RECAPTCHA !");
                }
                $error = curl_error($ch);
                curl_close($ch);

                $data = json_decode($response);

                return $this->defineNewPostsResult( $data[0]->id, $this->apiUrlBasePost.$data[0]->id, $data[0] );
            }
            elseif ($this->source->type_id === Source::TYPE_CUSTOM) 
            {
                // TYPE_CUSTOM
                $baseUrl = $this->source->url;
                $response = Http::get($baseUrl);
                if (!$response->ok()) {
                    throw new \Exception("Erro ao acessar {$baseUrl}: " . $response->status());
                }

                $crawler = new Crawler($response->body(), $baseUrl);

                $node = $crawler->filter($this->source->template->homeNew)->first();
                if (!$node->count()) {
                    throw new \Exception('Seletor de nova matéria não encontrou itens');
                }
                $newPostUrl = $node->attr('href');
                if (strpos($newPostUrl, 'http') !== 0) {
                    $newPostUrl = rtrim($baseUrl, '/') . '/' . ltrim($newPostUrl, '/');
                }

                return $this->defineNewPostsResult( 0, $newPostUrl );
            }
            elseif ($this->source->type_id === Source::TYPE_CUSTOM_LIST) 
            {
                $baseUrl = $this->source->template->listEndpoint;
                echo "$baseUrl\n";
                $crawler = $this->getBody($baseUrl);

                $method = "customList_".$this->source->id;
                $arrUrls = $this->$method($crawler);
                // $this->saveFile("valor2", $body); // para testar o que ele pega // storage/app/private...

                return $this->defineNewPostsResult( 0, $arrUrls );
            }
        }
        catch (\Throwable $e) {
             throw new \Exception($e->getMessage(), 0, $e);
        }

    }

    protected function defineNewPostsResult( $id, $endpoint, $data = null)
    {
        $endpoints = ( is_array($endpoint) ) ? $endpoint : [$endpoint];
        
        foreach( $endpoints as $endpoint )
        {
            $resultData = (object) [];
            $resultData->id       = $id;
            $resultData->data     = $data;
            $resultData->endpoint = $endpoint;

            $result[] = $resultData;
        }

        return $result;
    }

    #####################
    ### GET POST DATA ###
    public function getPostData( $sourcePostId ) 
    {
        $this->sourcePost = SourcePost::find($sourcePostId);
        if( $this->sourcePost->status_id != SourcePost::STATUS_PENDING ){
            return false;
        }

        $this->sourcePost->setStatus( SourcePost::STATUS_PROCESSING );
        try
        {
            if ( $this->source->type_id == Source::TYPE_WP ) 
            {
                echo "**WP**";
                $doc = $this->defineResultObj($this->sourcePost->post_data); // $postData
            }
            else 
            {
                echo "**CUSTOM**";
                $doc = $this->getCustomPostDataByUrl($this->sourcePost->endpoint);
                if (empty(trim($doc->content))) {
                    throw new \Exception('Conteúdo insuficiente');
                }
            }

            $this->sourcePost->doc = $doc;
            $this->sourcePost->status_id  = SourcePost::STATUS_DONE;
            $this->sourcePost->save();
            
            return true;
        }
        catch (\Throwable $e) 
        {
            $this->sourcePost->setStatus( SourcePost::STATUS_ERROR );
            $this->sourcePost->error = $e->getMessage(); // $e->serialize($e)
            $this->sourcePost->save();

            throw new \Exception("Erro ao buscar no source [{$this->sourcePost->endpoint}]", 0, $e);
        }
    }

    public function getCustomPostDataByUrl($url)
    {
        $response = Http::get($url);
        if (!$response->ok()) {
            throw new \Exception("Erro ao acessar {$url}: " . $response->status());
        }

        $crawler = new Crawler($response->body(), $url);
        $result = (object) [
            "sourceId"      => $this->source->id,
            "title"         => "",
            "content"       => "",
            "image"         => "",
            "url_original"  => $url,
            "image_caption" => "",
            "description"   => "",
            "category"      => 1,
            "post_id"       => 0,
        ];

        try {
            $imageUrl = $crawler->filterXPath('//meta[@property="og:image"]')->attr('content');    
            if ($this->testImageDownload($imageUrl)) {
                $result->image = $imageUrl;
                //echo "Imagem OK: " . $imageUrl . "\n";
            } else {
                $result->image = "";
                echo "Imagem inválida ou inacessível, ignorando: " . $imageUrl . "\n";
            }
        } catch (\Exception $e) {
            $result->image = "";
            echo "Erro ao extrair imagem: " . $e->getMessage() . "\n";
        }

        try {
            $result->title = $crawler->filter($this->source->template->title ?? 'h1')->first()->text();
        } catch (\Exception $e) {
            echo "Título não encontrado com seletor: " . ($this->source->template->title ?? 'h1') . "\n";
        }

        try {
            $result->description = $crawler->filterXPath('//meta[@property="og:description"]')->attr('content');
        } catch (\Exception $e) {
            $result->description = "";
            echo "Erro ao extrair descrição: " . $e->getMessage() . "\n";
        }

        try {
            $container = $crawler->filter($this->source->template->content)->first();
            $result->content = $container->html();
            $paraCount = $container->filter('p')->count();
            if ($paraCount < 2) {
                echo "Conteúdo com poucos parágrafos (menos de 2), ignorando.\n";
                $result->content = "";
            }
        } catch (\Exception $e) {
            echo "Conteúdo não encontrado com seletor: " . ($this->source->template->content) . "\n";
        }
        
        return $result;
    }

    private function testImageDownload($imageUrl)
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
            return false;
        }
    }

    private function defineResultObj( $postData )
    {
        $post = (object) [];

        $imageData = $this->getImage();

        $post->sourceId      = $this->source->id;
        $post->post_id       = $postData->id;
        $post->title         = $postData->title->rendered;
        $post->description   = $postData->yoast_head_json->description ?? strip_tags($postData->excerpt->rendered);
        $post->content       = $postData->content->rendered;
        $post->image         = $imageData->url;
        $post->image_caption = $imageData->caption;
        $post->category      = $this->getCategory();
        $post->url_original  = $postData->link;

        return $post;
    }

    private function getImage() 
    {
        $post = $this->sourcePost->post_data;

        if( !isset($post->featured_media) || $post->featured_media == 0 ) {
            return (object) ["url"=>"", "caption"=>""];
        }
        $imageApi = $this->getWp( $this->apiUrlBaseMedia . $post->featured_media );

        if (!empty($post->yoast_head_json->og_image[0]->url)) {
            $image = $post->yoast_head_json->og_image[0]->url;
        } elseif (!empty($imageApi->media_details->sizes->full->source_url)) {
            $image = $imageApi->media_details->sizes->full->source_url;
        } elseif (!empty($imageApi->source_url)) {
            $image = $imageApi->source_url;
        } elseif (!empty($imageApi->guid->rendered)) {
            $image = strip_tags($imageApi->guid->rendered);
        } else {
            $image = "";
        }
        $result["url"] = $image;
        $result["caption"] = $imageApi->alt_text ?? strip_tags($imageApi->caption->rendered);

        return (object) $result;
    }

    private function getCategory() 
    {
        $post = $this->sourcePost->post_data;

        $category = $this->getWp( $this->apiUrlBaseCategory .$post->categories[0])->name;

        return $category;
    }

    private function getWp( $endpoint ) 
    {
        try
        {        
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_ENCODING => 'gzip, deflate',
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Connection: keep-alive',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    'DNT: 1',
                    'Upgrade-Insecure-Requests: 1',
                ], 
            ]);
            
            $response = curl_exec($ch);
            
            if ($response === false) {
                echo "cURL Error: " . curl_error($ch) . "\n";
                echo "Endpoint: " . $endpoint . "\n";
                curl_close($ch);
                return null;
            }
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                echo "HTTP Error: " . $httpCode . "\n";
                echo "Endpoint: " . $endpoint . "\n";
                curl_close($ch);
                return null;
            }
            
            curl_close($ch);
            
            // Garantir que a resposta está em UTF-8
            if (!mb_check_encoding($response, 'UTF-8')) {
                $response = mb_convert_encoding($response, 'UTF-8', 'auto');
            }
            
            $decoded = json_decode($response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "JSON Error: " . json_last_error_msg() . "\n";
                echo "Response: " . substr($response, 0, 500) . "...\n";
                return null;
            }
            
            return $decoded;
        }
        catch (\Throwable $e) 
        {
            throw new \Exception("Erro ao buscar dados do post", 0, $e);
        }
    }


    

    function fetchCrawler(string $url): Crawler
    {
        // Criando o cliente HTTP
        $client = new Client([
            'allow_redirects' => true, // Seguir redirecionamentos
            'timeout' => 15,           // Timeout em segundos
            'verify' => true,          // Verifica SSL
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
            ],
        ]);

        try {
            $response = $client->get($url);

            $body = (string) $response->getBody();
            $baseUrl = $response->getHeaderLine('X-Guzzle-Effective-Url') ?: $url;

            $crawler = new Crawler($body, $baseUrl);

            return $crawler;

        } catch (\Exception $e) {
            throw new \RuntimeException("Erro ao acessar a URL: " . $e->getMessage());
        }
    }

    public function getBody($url)
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        ])->get($url);

        if ($response->failed()) {
            throw new \Exception("Erro ao acessar a URL: $url");
        }

        $crawler = new Crawler($response->body(), $url);

        return $crawler;
    }

    public function saveFile( $fileName, $content )
    {
        $fileName = $fileName."_".date("Ymd_his").".html";
        Storage::put($fileName, $content);

        return 'Conteúdo salvo em storage/app/body.html';
    }

    #################################
    ## CustomList por SOURCES ID
    public function customList_1($crawler) # Estadão
    {
        $body = $crawler->filter('body')->text();

        preg_match_all('/"canonical_url"\s*:\s*"([^"]+)"/', $body, $matches);

        $urls = $matches[1] ?? [];

        return $urls;
    }
    public function customList_2($crawler) # Valor Economico
    {
        $urls = [];
        $links = $crawler->filter('.bastian-page .feed-post-link')->each(function ($node) {
            return $node->attr('href');
        });

        foreach ($links as $link) {
            if( $link ){
                $urls[] = $link;
            }
        }

        return $urls;
    }
    public function customList_16($crawler) # O Globo
    {
        $urls = [];
        $links = $crawler->filter('.bastian-page .feed-post-link')->each(function ($node) {
            return $node->attr('href');
        });

        foreach ($links as $link) {
            if( $link ){
                $urls[] = $link;
            }
        }

        return $urls;
    }

}