<?php

namespace App\Services;

use App\Models\WebsitePost;

class PostPublishService
{
    public $wPost;
    public $websiteUrl;
    public $credentials;
    public $sourceCitation;
    public $sourceLink;
    public $siteMapUrl;
    public $defaultPostStatus;
    public $defaultImageId;

    public function __construct( $websitePost )
    {
        $this->wPost             = $websitePost;
        $this->websiteUrl        = $websitePost->Website->url."/";
        $this->defaultPostStatus = $websitePost->WebsiteSource->doc->defaultPostStatus;
        $this->defaultImageId    = ""; // 67

        $this->credentials = (object)[
            // "user" => $websitePost->Website->config->wpUser,
            // "pass" => $websitePost->Website->config->wpPass,
            "auth" => base64_encode($websitePost->Website->config->wpUser. ':' . $websitePost->Website->config->wpPass)
        ];
        
        $this->sourceCitation    = ( $websitePost->source->name ) ?? false;
        $this->sourceLink        = ( $websitePost->url_original ) ?? false; 

        $this->siteMapUrl = $this->websiteUrl."wp-sitemap-posts-post-1.xml";
    }

    public function run() 
    {
        $this->wPost->setStatus(WebsitePost::STATUS_PROCESSING);
        
        $title       = $this->wPost->post_title;
        $description = $this->wPost->post_description;
        
        $imageId     = $this->defineImage();
        $categoryId  = $this->defineCategory( $this->wPost->post_category );
        $content     = $this->defineContent( $this->wPost->post_content );

        $postId = $this->publish( $title, $description, $content, $imageId, $categoryId );
        
        if( $postId )
        {
            self::metaRankMath($postId, $title, $description, $content, $imageId, $this->wPost->seo_data, $categoryId);
            
            $this->wPost->website_post_id = $postId;
            // $this->wPost->website_post_url = $postId;
            $this->wPost->status_id = WebsitePost::STATUS_DONE;
            $this->wPost->save();
        }

        return $postId;
    }

    ##################
    # DEFINE METHODS #
    protected function defineCategory( $postCategory )
    {
        if( $postCategory == 1 ){
            return (int) $postCategory;
        }

        $categorySlug = removeAccents( $postCategory );
        
        # Verifica se ja existe
        $response = self::makeCurlRequest(
            $this->websiteUrl . "wp-json/wp/v2/categories?slug=" . $categorySlug
        );
        
        $result = json_decode($response['result'], true);

        foreach ($result as $key => $category) 
        {
            if($category['name'] == $postCategory)
            {
                $responseObj = json_decode($response['result']);
                
                if (!$responseObj[0] || !isset($responseObj[0]->id)) {
                    echo "defineCategory: Erro buscar categoria - " . $response['result'];
                    return false;
                }
                return $responseObj[0]->id;
            }
        }

        # Cria categoria se não existir
        $response = self::makeCurlRequest(
            $this->websiteUrl."wp-json/wp/v2/categories",
            'POST',
            ['name' => $postCategory]
        );

        $responseObj = json_decode($response['result']); //var_dump( $responseObj->id);
        if (!$responseObj || !isset($responseObj->id)) {
            echo "defineCategory: Erro ao criar categoria - " . $response['result'];
            return false;
        }

        return $responseObj->id;
    }

    protected function defineImage()
    {
        if( !$this->wPost->post_image ){
            echo "Nenhuma imagem fornecida - continuando sem imagem\n";
            return null;
        }
        
        if( $this->wPost->website_image_id ) {
            return $this->wPost->website_image_id;
        }
        
        $url = $this->websiteUrl . 'wp-json/wp/v2/media';
        $uploadedImageId = self::uploadFile($url, $this->wPost->post_image, $this->wPost->post_image_caption);
        
        if( $uploadedImageId ) {
            $this->wPost->website_image_id = $uploadedImageId;
            $this->wPost->save();
            echo "Imagem enviada com sucesso - ID: " . $uploadedImageId . "\n";
            return $uploadedImageId;
        } else {
            echo "Falha no upload da imagem - continuando sem imagem\n";
            return null;
        }
    }

    protected function defineContent( $postContent )
    {
        if( $this->sourceLink ){
            $sourceLink = '<a href="'.$this->sourceLink.'" rel="noopener nofollow noreferrer" target="_blank">'.$this->sourceCitation.'</a>';
        }
        if( $this->sourceCitation ){
            $sourceDesc = ($this->sourceLink) ? $sourceLink : $this->sourceCitation;
            $citation = '<p><small>Fonte por: '.$sourceDesc.'</small></p>';
        }
        

        $postContent.= $citation;

        return $postContent;
    }

    protected function publish( $title, $description, $content, $imageId, $categoryId )
    {
        try 
        {
            $data = [
                'title'    => $title,
                'content'  => $content,
                'excerpt'  => $description,
                'categories' => [is_numeric((int)$categoryId) ? (int)$categoryId : 1],
                'status'   => $this->defaultPostStatus
            ];

            if ( !empty($imageId) && is_numeric($imageId) ) {
                $data['featured_media'] = (int)$imageId;
                echo "Usando imagem como destaque - ID: " . $imageId . "\n";
            } else {
                echo "Publicando sem imagem de destaque\n";
            }

            $response = self::makeCurlRequest(
                $this->websiteUrl . "wp-json/wp/v2/posts",
                'POST',
                $data
            );
            
            if ($response['httpCode'] !== 201) {
                echo "PostPublishService->publish L168: Erro ao publicar - " . $response['httpCode'];
                return false;
            }

            $post = json_decode($response['result']);
            if (!$post || !isset($post->id)) {
                echo "PostPublishService->publish L174: Erro ao publicar - " . $response['httpCode'];
                return false;
            }

            return $post->id;
        } 
        catch (\Exception $e) {
            //dd($e);
        }
    }


    #######
    # AUX #
    private function makeCurlRequest($url, $method = 'GET', $data = null, $headers = []) 
    {
        try 
        {
            //error_log("DEBUG 4: POSTFIELDS = " . json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $ch = curl_init();
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 3000,
                CURLOPT_HTTPHEADER => array_merge([
                    'Content-Type: application/json',
                    'Authorization: Basic ' . $this->credentials->auth,
                ], $headers)
            ];

            if ($method === 'POST') {
                $options[CURLOPT_POST] = 1;
                if ($data) {
                    $options[CURLOPT_POSTFIELDS] = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    $options[CURLOPT_HTTPHEADER][] = 'Content-Length: ' . strlen($options[CURLOPT_POSTFIELDS]);
                }
            } elseif ($method !== 'GET') {
                $options[CURLOPT_CUSTOMREQUEST] = $method;
                if ($data) {
                    $options[CURLOPT_POSTFIELDS] = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    $options[CURLOPT_HTTPHEADER][] = 'Content-Length: ' . strlen($options[CURLOPT_POSTFIELDS]);
                }
            }

            if (isset($options[CURLOPT_POSTFIELDS])) {
                //error_log("DEBUG 5: POSTFIELDS = " . $options[CURLOPT_POSTFIELDS]);
            }

            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return ['result' => $result, 'httpCode' => $httpCode];
        } 
        catch (\Exception $e) {
            //dd( $e );
        }
    }

    private function uploadFile($url, $archivo, $caption = '') 
    {
        if (empty($archivo) || empty($url)) {
            return null;
        }

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => trim($archivo),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                CURLOPT_HTTPHEADER => [
                    'Accept: image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Accept-Encoding: gzip, deflate',
                    'Connection: keep-alive',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    'DNT: 1',
                    'Upgrade-Insecure-Requests: 1',
                ],
                CURLOPT_ENCODING => 'gzip, deflate'
            ]);
            
            $fileData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($fileData === false || $httpCode !== 200) {
                echo "Erro ao baixar arquivo: " . $httpCode . "\n";
                return null;
            }

            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $contentLength = strlen($fileData);

            //echo "Debug - URL: " . $archivo . "\n";
            //echo "Debug - Content-Type: " . $contentType . "\n";
            //echo "Debug - Content-Length: " . $contentLength . "\n";
            
            if (strpos($contentType, 'image/') !== 0) {
                echo "Arquivo inválido - Tipo: " . $contentType . ", Tamanho: " . $contentLength . "\n";
                return null;
            }

            $parsedUrl = parse_url($archivo);
            $pathInfo = pathinfo($parsedUrl['path']);
            $filename = $pathInfo['filename'] . '.' . $pathInfo['extension'];

            if (empty($pathInfo['extension'])) {
                $extension = 'jpg'; // default
                if (strpos($contentType, 'jpeg') !== false) {
                    $extension = 'jpg';
                } elseif (strpos($contentType, 'png') !== false) {
                    $extension = 'png';
                } elseif (strpos($contentType, 'gif') !== false) {
                    $extension = 'gif';
                } elseif (strpos($contentType, 'webp') !== false) {
                    $extension = 'webp';
                }
                $filename = 'image_' . uniqid() . '.' . $extension;
            }

            //echo "Debug - Filename: " . $filename . "\n";
            //echo "Debug - Upload URL: " . $url . "\n";

            $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
            file_put_contents($tempFile, $fileData);

            $postData = [
                'file' => new \CURLFile($tempFile, $contentType, $filename),
                'alt_text' => $caption,
                'caption' => $caption
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 3000,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic ' . $this->credentials->auth,
                ],
                CURLOPT_POSTFIELDS => $postData,
            ]);
            
            $result = curl_exec($curl);
            $err = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            unlink($tempFile);
            
            if ($err) {
                echo "Erro cURL: " . $err . "\n";
                return null;
            }

            //echo "Debug - Upload HTTP Code: " . $httpCode . "\n";
            //echo "Debug - Upload Response: " . $result . "\n";
            
            if ($httpCode !== 201) {
                echo "Erro HTTP: " . $httpCode . "\n";
                echo "Resposta: " . $result . "\n";
                return null;
            }

            $response = json_decode($result);
            if (!$response || !isset($response->id)) {
                echo "Resposta inválida: " . $result . "\n";
                return null;
            }

            echo "Arquivo enviado com sucesso: " . $archivo . "\n";
            return $response->id;
        } catch (\Exception $e) {
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
            echo "Erro ao fazer upload do arquivo: " . $e->getMessage() . "\n";
            return null;
        }
    }

    private function metaRankMath($post_id, $title, $description, $content, $imageId, $seoData = null, $category) 
    {
        try 
        {
            # KeyWords
            $keywords = $seoData->keywords;
            if ( empty($keywords) ) 
            {
                echo "\n PostPublishService->metaRankMath L:321 - '$ seoData->keywords' veio vazio! \n";
                $contentWords = array_filter(explode(' ', strip_tags($content)));
                $titleWords   = array_filter(explode(' ', $title));
                $keywords     = array_slice(array_unique(array_merge($titleWords, $contentWords)), 0, 5);
            }

            if (!empty($keywords)) {
                self::updatePostTags($post_id, $keywords);
            }

            # FocusKeyWords
            $focusKeyword = '';
            if (!empty($seoData->focus_keyword)) {
                $focusKeyword = $seoData->focus_keyword;
            } 
            elseif (!empty($keywords[0])) {
                $focusKeyword = $keywords[0];
            } 
            elseif (!empty($title)) {
                $focusKeyword = $title;
            }
            else {
                echo "\n PostPublishService->metaRankMath L:344 - '$ focusKeyword' veio vazio! \n";
            }

            $addMetaIfNotEmpty = function($key, $value) use (&$yoastMeta) {
                $trimmedValue = trim((string)$value);
                if ($trimmedValue !== '') {
                    $yoastMeta[$key] = $trimmedValue;
                }
            };

            $yoastMeta = [];

            $addMetaIfNotEmpty('_yoast_wpseo_title', isset($seoData->title) ? $seoData->title : $title);
            $addMetaIfNotEmpty('_yoast_wpseo_metadesc', isset($seoData->description) ? $seoData->description : $description);
            $addMetaIfNotEmpty('_yoast_wpseo_focuskw', $focusKeyword ?? '');
            $addMetaIfNotEmpty('_yoast_wpseo_opengraph-title', isset($seoData->title) ? $seoData->title : $title);
            $addMetaIfNotEmpty('_yoast_wpseo_opengraph-description', isset($seoData->description) ? $seoData->description : (substr(strip_tags($content), 0, 160) ?: $description));
            $addMetaIfNotEmpty('_yoast_wpseo_twitter-title', isset($seoData->title) ? $seoData->title : $title);
            $addMetaIfNotEmpty('_yoast_wpseo_twitter-description', isset($seoData->description) ? $seoData->description : (substr(strip_tags($content), 0, 160) ?: $description));

            $postData = [
                'meta' => $yoastMeta
            ];
            
            $response = self::makeCurlRequest(
                $this->websiteUrl . "wp-json/wp/v2/posts/{$post_id}",
                'POST',
                $postData
            );

            return $response['httpCode'] === 200;

        } catch (\Exception $e) {
            return false;
        }
    }

    private function updatePostTags($post_id, $tags) 
    {
        try 
        {
            $tagIds = [];
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $tag = str_replace('"', '', $tag);
                
                if (count(explode(' ', $tag)) > 5) {
                    continue;
                }
                
                $tagSlug = removeAccents($tag);
                $response = self::makeCurlRequest(
                    $this->websiteUrl . "wp-json/wp/v2/tags?slug=" . $tagSlug
                );

                $result = json_decode($response['result'], true);
                $tagId = false;
                
                foreach ($result as $key => $tagData) {
                    if($tagData['name'] == $tag){
                        $tagId = $tagData['id'];
                        break;
                    }
                }

                if($tagId === false) 
                {
                    $createTagResponse = self::makeCurlRequest(
                        $this->websiteUrl . "wp-json/wp/v2/tags",
                        'POST',
                        ['name' => $tag]
                    );
                    
                    if ($createTagResponse['httpCode'] === 201) {
                        $tagData = json_decode($createTagResponse['result'], true);
                        $tagId = $tagData['id'];
                    }
                }

                if($tagId !== false) {
                    $tagIds[] = $tagId;
                }
            }

            $postData = [
                'tags' => $tagIds
            ];

            $response = self::makeCurlRequest(
                $this->websiteUrl . "wp-json/wp/v2/posts/{$post_id}",
                'POST',
                $postData
            );

            return $response['httpCode'] === 200;
        } 
        catch (\Exception $e) {
            return false;
        }
    }
}