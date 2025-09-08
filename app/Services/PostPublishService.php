<?php

namespace App\Services;

use App\Models\WebsitePost;

class PostPublishService
{
    public $wPost;
    public $websiteUrl;
    public $credentials;
    public $sourceCitation;
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
            "user" => $websitePost->Website->config->wpUser,
            "pass" => $websitePost->Website->config->wpPass,
        ];
        $this->credentials->auth = base64_encode($this->credentials->user. ':' . $this->credentials->pass);
        $this->sourceCitation    = ( $websitePost->source->name ) ?? false;

        $this->siteMapUrl = $this->websiteUrl."wp-sitemap.xml";
    }

    public function run() 
    {
        // $this->wPost->setStatus( WebsitePost::STATUS_PROCESSING);
        
        $title       = $this->wPost->post_title;
        $description = $this->wPost->post_description;
        
        $imageId     = $this->defineImage();
        $categoryId  = $this->defineCategory( $this->wPost->post_category );
        $content     = $this->defineContent( $this->wPost->post_content, $this->wPost->seo_data );

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
        if( !$this->wPost->website_image_id )
        {
            $url = $this->websiteUrl . 'wp-json/wp/v2/media';
            $this->wPost->website_image_id = ( self::uploadFile($url, $this->wPost->post_image, $this->wPost->post_image_caption) ) ?? $this->defaultImageId;

            $this->wPost->save();
        }

        return $this->wPost->website_image_id;
    }

    protected function defineContent( $postContent, $seoData )
    {
        $keyWords = array_filter($seoData->keywords, function($word) {
            return strlen($word) > 3;
        });
        $keyWords = array_values($keyWords);

        $optimizedContent = self::optimizeContent( $postContent, $keyWords);

        if( $this->sourceCitation ){
            // <a href="'.$post[0]['url_original'].'" rel="noopener nofollow noreferrer" target="_blank"></a> 
            $optimizedContent = $optimizedContent.'<p><small>Fonte por: '.$this->sourceCitation.'</small></p>';
        }

        return $optimizedContent;
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
            }

            $response = self::makeCurlRequest(
                $this->websiteUrl . "wp-json/wp/v2/posts",
                'POST',
                $data
            );
            
            if ($response['httpCode'] !== 201) {
                echo "PostPublishService->publish L157: Erro ao publicar - " . $response['httpCode'];
                return false;
            }

            $post = json_decode($response['result']);
            if (!$post || !isset($post->id)) {
                echo "PostPublishService->publish L163: Erro ao publicar - " . $response['httpCode'];
                return false;
            }

            return $post->id;
        } 
        catch (\Exception $e) {
            dd($e);
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
            dd( $e );
        }
    }

    private function uploadFile($url, $archivo, $caption = '') 
    {
        try 
        {
            $data = file_get_contents(trim($archivo));
            if ($data === false) return null;

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 100,
                CURLOPT_TIMEOUT => 3000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic ' . $this->credentials->auth,
                    "cache-control: no-cache",
                    "Content-Disposition: attachment; filename=".basename($archivo),
                    "alt_text: ".$caption,
                    "caption: ".$caption
                ],
                CURLOPT_POSTFIELDS => $data,
            ]);
            
            $result = curl_exec($curl);
            $err = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($err) return dd($err);

            if ($httpCode !== 201) return dd($httpCode);

            $response = json_decode($result);
            if (!$response || !isset($response->id)) return dd($response);

            return $response->id;
        } 
        catch (\Exception $e) {
            dd( $e );
        }
    }

    private function optimizeContent( $content, $postKeyWords ) 
    {
        try 
        {
            $sitemapUrls = [];

            $sitemapContent = file_get_contents( $this->siteMapUrl );
            if ($sitemapContent) {
                preg_match_all('/<loc>(.*?)<\/loc>/', $sitemapContent, $matches);
                foreach ($matches[1] as $sitemapUrl) {
                    if (strpos($sitemapUrl, 'wp-sitemap-posts-post') !== false) {
                        $postSitemap = file_get_contents($sitemapUrl);
                        if ($postSitemap) {
                            preg_match_all('/<loc>(.*?)<\/loc>/', $postSitemap, $postMatches);
                            $sitemapUrls = array_merge($sitemapUrls, $postMatches[1]);
                        }
                    }
                }
            }

            foreach ( $postKeyWords as $keyword ) 
            {
                $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';
                $content = preg_replace($pattern, '<strong>$0</strong>', $content, 1);
            }

            foreach ( $postKeyWords as $keyword ) 
            {
                foreach ($sitemapUrls as $url) 
                {
                    $urlTitle = basename($url);
                    $urlTitle = urldecode($urlTitle);
                    if (stripos($urlTitle, $keyword) !== false) 
                    {
                        $pattern = '/(?<!<h[2-4][^>]*>)(?<!<strong[^>]*>)\b(' . preg_quote($keyword, '/') . ')\b/i';
                        $replacement = '<a href="' . $url . '">$1</a>';

                        $new_content = @preg_replace($pattern, $replacement, $content, 1);
                        if ($new_content !== null && $new_content !== $content) {
                            $content = $new_content;
                            break;
                        }
                    }
                }
            }

            return $content;
        } 
        catch (\Exception $e) {
            dd($e);
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

            $yoastMeta = [
                'meta' => [
                    [
                        'key' => '_yoast_wpseo_focuskw',
                        'value' => $focusKeyword
                    ],
                    [
                        'key' => '_yoast_wpseo_metadesc',
                        'value' => !empty($seoData->description) ? $seoData->description : (substr(strip_tags($content), 0, 160) ?: $description)
                    ],
                    [
                        'key' => '_yoast_wpseo_title',
                        'value' => !empty($seoData->title) ? $seoData->title : $title
                    ],
                    [
                        'key' => '_yoast_wpseo_opengraph-title',
                        'value' => !empty($seoData->title) ? $seoData->title : $title
                    ],
                    [
                        'key' => '_yoast_wpseo_opengraph-description',
                        'value' => !empty($seoData->description) ? $seoData->description : (substr(strip_tags($content), 0, 160) ?: $description)
                    ],
                    [
                        'key' => '_yoast_wpseo_twitter-title',
                        'value' => !empty($seoData->title) ? $seoData->title : $title
                    ],
                    [
                        'key' => '_yoast_wpseo_twitter-description',
                        'value' => !empty($seoData->description) ? $seoData->description : (substr(strip_tags($content), 0, 160) ?: $description)
                    ]
                ]
            ];

            $postData = [
                'title'      => $title,
                'content'    => $content,
                'status'     => $this->defaultPostStatus,
                'categories' => [$category],
                'meta'       => $yoastMeta['meta']
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