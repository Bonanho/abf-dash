<?php

namespace App\Services;

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

        $this->apiUrlBase         = $this->source->url . "/wp-json/wp/v2/" ;
        $this->apiUrlBasePost     = $this->source->url . "/wp-json/wp/v2/posts/" ;
        $this->apiUrlBaseMedia    = $this->source->url . "/wp-json/wp/v2/media/" ;
        $this->apiUrlBaseCategory = $this->source->url . "/wp-json/wp/v2/categories/" ;
    }

    #####################
    ### GET NEW POSTS ###
    public function fetchValidation()
    {
        // $data = $this->fetchNewPost();

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
        
        if( $data ) 
        {
            $this->source->status_id = Source::STATUS_ACTIVE;
            $this->source->save();
        }
        else
        {
            $this->source->status_id = Source::STATUS_INVALID;
            $this->source->type_id   = Source::TYPE_CUSTOM;
            $this->source->doc       = "{'test-url':$apiUrl}";
            $this->source->save();

            echo "\n".$apiUrl."\n";
        }
        
        return $this->source->status_id;
    }

    public function fetchNewPost()
    {
        try
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

            return $this->defineNewPostsResult( $data );
        }
         catch (\Throwable $e) {
            throw new \Exception("Erro ao buscar no source", 0, $e);
        }

    }

    protected function defineNewPostsResult( $data )
    {
        $resultData = (object) [];

        $resultData->id       = $data[0]->id;
        $resultData->endpoint = $this->apiUrlBasePost . $resultData->id;
        $resultData->data     = $data[0];

        $result[] = $resultData;

        return $result;
    }

    #####################
    ### GET POST DATA ###
    public function getPostData( $sourcePostId ) 
    {
        $this->sourcePost = SourcePost::find($sourcePostId);
        // if( $this->sourcePost->status_id != SourcePost::STATUS_PENDING ){
        //     return false;
        // }

        $this->sourcePost->setStatus( SourcePost::STATUS_PROCESSING );

        try
        {
            // **** Somente para testes e comparação ****
            $postData = $this->getWp( $this->sourcePost->endpoint );

            $this->sourcePost->post_data2 = $postData;
            $this->sourcePost->save();
            // **** Somente para testes e comparação ****

            $doc = $this->defineResultObj( $this->sourcePost->post_data ); // $postData
            $this->sourcePost->doc = $doc;
            $this->sourcePost->status_id  = SourcePost::STATUS_DONE;
            $this->sourcePost->save();

            return true;
        }
        catch (\Throwable $e) 
        {
            $this->sourcePost->setStatus( SourcePost::STATUS_ERROR );
            $this->sourcePost->post_data2 = $e->getMessage(); // $e->serialize($e)
            $this->sourcePost->save();

            throw new \Exception("Erro ao buscar no source [{$this->sourcePost->endpoint}]", 0, $e);
        }
    }


    private function defineResultObj( $postData )
    {
        $post = (object) [];

        $imageData = $this->getImage();

        $post->sourceId      = $this->source->id;
        $post->post_id       = $postData->id;
        $post->title         = $this->filterWords( $postData->title->rendered );
        $post->description   = $postData->yoast_head_json->description ?? strip_tags($postData->excerpt->rendered);
        $post->content       = $this->formatContent( $postData->content->rendered );
        $post->image         = $imageData->url;
        $post->image_caption = $imageData->caption;
        $post->category      = $this->getCategory();
        $post->url_original  = $postData->link;

        return $post;
    }

    ###########
    ### AUX ###
    private function filterWords( $text ) 
    {
        $palavrasBloqueadas = [ 'Metrópoles', 'CNN', 'Adrenaline', 'Jornal Cidade', 'O Antagonista', 'Antagonista', 'Mundo Conectado', 'Poder 360', 'Tribuna do Norte' ];
        $textLower = mb_strtolower($text, 'UTF-8');
        foreach($palavrasBloqueadas as $palavra) {
            if(mb_strpos($textLower, mb_strtolower($palavra, 'UTF-8')) !== false) {
                return true;
            }
        }

        return $textLower;
    }

    private function getImage() 
    {
        $post = $this->sourcePost->post_data2;

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
        $post = $this->sourcePost->post_data2;

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

    private function formatContent( $content ) 
    {
        // Garantir que o conteúdo está em UTF-8
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        }

        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/isu', '', $content);
        $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/isu', '', $content);
        $content = preg_replace('/<footer\b[^>]*>(.*?)<\/footer>/isu', '', $content);
        $content = preg_replace('/<header\b[^>]*>(.*?)<\/header>/isu', '', $content);
        $content = preg_replace('/<nav\b[^>]*>(.*?)<\/nav>/isu', '', $content);
        $content = preg_replace('/<aside\b[^>]*>(.*?)<\/aside>/isu', '', $content);
        $content = preg_replace('/<div\b[^>]*>(.*?)<\/div>/isu', '', $content);
        $content = preg_replace('/<article\b[^>]*>(.*?)<\/article>/isu', '', $content);
        $content = preg_replace('/<figcaption\b[^>]*>(.*?)<\/figcaption>/isu', '', $content);
        $content = preg_replace('/<figure\b[^>]*>(.*?)<\/figure>/isu', '', $content);
        $content = preg_replace('/<h6\b[^>]*>(.*?)<\/h6>/isu', '', $content);
        // Preservar imagens inline; ainda removemos SVGs por segurança
        $content = preg_replace('/<img\b[^>]*>/isu', '', $content);
        $content = preg_replace('/<svg\b[^>]*>/isu', '', $content);

        $content = str_replace(['<br>', '<br/>', '<br />'], "\n", $content); 
        // Usar strip_tags com encoding UTF-8

        // Incluímos <img> para manter imagens inline e seus atributos (src, alt, etc.)
        $content = strip_tags($content, '<p><br><strong><b><em><i><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code>');
        $content = preg_replace('/<(p|br|strong|b|em|i|ul|ol|li|h1|h2|h3|h4|h5|h6|blockquote|pre|code)[^>]*>/iu', '<$1>', $content);
        
        $content = preg_replace('/\s+/u', ' ', $content);
        $content = trim($content);
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $content;
    }
}