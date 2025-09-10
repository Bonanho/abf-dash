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
        $this->wPost->setStatus( WebsitePost::STATUS_PROCESSING);
        
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
        if( !$this->wPost->website_image ){
            return 0;
        }
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

        $optimizedContent = self::optimizeContent( $postContent, $keyWords, $seoData);

        if( $this->sourceLink ){
            $sourceLink = '<a href="'.$this->sourceLink.'" rel="noopener nofollow noreferrer" target="_blank">'.$this->sourceCitation.'</a>';
        }
        if( $this->sourceCitation ){
            $sourceDesc = ($this->sourceLink) ? $sourceLink : $this->sourceCitation;
            $citation = '<p><small>Fonte por: '.$sourceDesc.'</small></p>';
        }
        

        $optimizedContent.= $citation;

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
            elseif( $imageId===0 ){
                $data['featured_media'] = 0;
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
            
            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
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
            
            if ($err) return dd("Erro", $err);

            if ($httpCode !== 201) return dd("HttpCode", $httpCode, $result);

            $response = json_decode($result);
            if (!$response || !isset($response->id)) return dd("Response",$response, $result);

            return $response->id;
        } 
        catch (\Exception $e) {
            dd( $e );
        }
    }

    private function optimizeContent( $content, $postKeyWords, $seoData ) 
    {
        try 
        {
            foreach ( $postKeyWords as $keyword ) 
            {
                $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';
                $content = preg_replace($pattern, '<strong>$0</strong>', $content, 1);
            }

            $sitemapUrls = [];
            $postSitemapUrl = $this->siteMapUrl; //str_replace('admin.', "", $this->websiteUrl) . 'wp-sitemap-posts-post-1.xml';

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $postSitemapUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                CURLOPT_HTTPHEADER => [
                    'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
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
            
            $postSitemap = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($postSitemap && $httpCode === 200) {
                preg_match_all('/<loc>(.*?)<\/loc>/', $postSitemap, $postMatches);
                if (!empty($postMatches[1])) {
                    $sitemapUrls = $postMatches[1];
                }
            }
            
            $linkCount = 0;           
            $titleWords = explode(' ', $seoData->title);
            foreach ($titleWords as $word) {
                if ($linkCount >= 3) break;
                $wordFormat = removeAccents(strtolower($word));
                if (empty($wordFormat) || strlen($wordFormat) <= 5) continue;
                foreach ($sitemapUrls as $url) {
                    if (strpos($url, $wordFormat) !== false) {
                        
                        $wordFound = (stripos($content, $word) !== false) || (stripos($content, $wordFormat) !== false);
                        if (!$wordFound) {
                            continue;
                        }
                        
                        $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
                        $parts = preg_split('~(<[^>]+>)~', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
                        $didReplace = false;
                        $tagDepth = [ 'a' => 0, 'strong' => 0, 'code' => 0, 'pre' => 0, 'script' => 0, 'noscript' => 0, 'style' => 0, 'h2' => 0, 'h3' => 0, 'h4' => 0, 'h5' => 0, 'h6' => 0, ];
                        
                        for ($pi = 0; $pi < count($parts); $pi++) {
                            $segment = $parts[$pi];
                            if ($segment !== '' && $segment[0] === '<') {
                                if (preg_match('~^<\s*(/)?\s*([a-zA-Z][a-zA-Z0-9]*)\b~', $segment, $m)) {
                                    $isClosing = !empty($m[1]);
                                    $tagName = strtolower($m[2]);
                                    if (isset($tagDepth[$tagName])) {
                                        if ($isClosing) {
                                            if ($tagDepth[$tagName] > 0) {
                                                $tagDepth[$tagName]--;
                                            }
                                        } else {
                                            if (!preg_match('~/\s*>$~', $segment)) {
                                                $tagDepth[$tagName]++;
                                            }
                                        }
                                    }
                                }
                                continue;
                            }
                            
                            $insideForbidden = false;
                            foreach ($tagDepth as $depth) {
                                if ($depth > 0) { $insideForbidden = true; break; }
                            }
                            if ($insideForbidden) { continue; }
                            
                            $segmentReplaced = preg_replace($pattern, '<a href="' . $url . '">$0</a>', $segment, 1);
                            if ($segmentReplaced !== null && $segmentReplaced !== $segment) {
                                $parts[$pi] = $segmentReplaced;
                                $didReplace = true;
                                break;
                            }
                        }
                        
                        if ($didReplace) {
                            $new_content = implode('', $parts);
                        } else {
                            $new_content = $content;
                        }
                        
                        if ($new_content === null) {
                            continue;
                        }
                        
                        if ($new_content !== $content) {
                            $content = $new_content;
                            $linkCount++;
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
                'content' => $content,
                'meta' => $yoastMeta
            ];

            $postData = [
                'content' => $content,
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