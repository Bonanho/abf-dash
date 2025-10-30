<?php

namespace App\Services;

use App\Models\WebsitePostQueue;

class PostProcessService
{
    public $websitePostQueue;

    public function getPostsToProcess( $typeId )
    {
        $websitePostsQueue = WebsitePostQueue::where("status_id",WebsitePostQueue::STATUS_PENDING)->orderBy("id","asc");
        if( $typeId ){
            $websitePostsQueue = $websitePostsQueue->where("type_id",$typeId);
        }
        $websitePostsQueue = $websitePostsQueue->get();

        return $websitePostsQueue;
    }
    
    public function run( $wpqId )
    {
        $this->websitePostQueue = WebsitePostQueue::find( $wpqId );
        if( $this->websitePostQueue->status_id != WebsitePostQueue::STATUS_PENDING || !$this->websitePostQueue->SourcePost ){
            return false;
        }

        $this->websitePostQueue->setStatus( WebsitePostQueue::STATUS_PROCESSING );

        $postParams = $this->websitePostQueue->SourcePost->doc;

        $processedParams = (object) [];

        $shouldRewrite = ($this->websitePostQueue->type_id == WebsitePostQueue::TYPE_REWRITE) ? true : false;

        if( $this->websitePostQueue->type_id == WebsitePostQueue::TYPE_COPY )
        {
            $processedParams->title       = strip_tags($postParams->title);
            $processedParams->description = strip_tags($postParams->description);
        }
        elseif( $this->websitePostQueue->type_id == WebsitePostQueue::TYPE_REWRITE )
        {
            echo "title - ";
            $title = $this->rewriteAi( $postParams->title, 'title', $shouldRewrite );
            $processedParams->title = substr($title, -1) == '.' ? substr($title, 0, -1) : $title;

            echo "description - ";
            $processedParams->description = $this->rewriteAi( $postParams->description, 'description', $shouldRewrite );
            if (mb_strlen($processedParams->description) > 160) {
                 $processedParams->description = mb_substr($processedParams->description, 0, 160) . '...';
            }
        }

        echo "content AI (".$this->websitePostQueue->getType().") - ";
        $processedParams->content = $this->rewriteAi( $postParams->content, 'text', $shouldRewrite );

        echo "SEO - ";
        $seoData = SeoAiService::optimizeSeo($processedParams->title, $processedParams->description, $processedParams->content );
        $processedParams->seoData = (object) $seoData;

        // otimização final de conteúdo após SEO (usa keywords e sitemap)
        $keywords = $processedParams->seoData->keywords ?? [];
        $sitemap  = @$this->websitePostQueue->Website->config->siteMap ?? $this->websitePostQueue->Website->url.'post-sitemap.xml';
        try {
            $processedParams->content = self::optimizeContent($processedParams->title, $processedParams->content, $keywords, $this->websitePostQueue->Website->url.$sitemap);
        } 
        catch (\Exception $e) 
        {

        }

        return $processedParams;
    }
    
    public function rewriteAi( $text, $type=null, $shouldRewrite = true )
    {
        $result = RewriterAiService::getResultsAi( $text, $type, $shouldRewrite );

        if ( is_object($result) && $result->type=="Error") 
        {
            $this->websitePostQueue->status_id = WebsitePostQueue::STATUS_ERROR;
            $this->websitePostQueue->doc = [ "erro"=>"Erro na reescrita de IA - Tipo: $type -- ".$result->message ];
            $this->websitePostQueue->save();
            throw new \Exception("Erro na reescrita de IA - Tipo: $type -- ".$result->message);
        }
        return $result;
    }

    /**
     * Verifica se o sitemap é direto ou um index e retorna a URL final do sitemap
     * Se for um index, busca o sitemap mais atualizado baseado no lastmod
     */
    private function getFinalSitemapUrl($sitemapUrl)
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $sitemapUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30,
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
            
            $sitemapContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if (!$sitemapContent || $httpCode !== 200) {
                return $sitemapUrl; // Retorna a URL original se houver erro
            }
            
            // Verifica se é um sitemap index (contém <sitemapindex>)
            if (strpos($sitemapContent, '<sitemapindex>') !== false) {
                $sitemaps = $this->parseSitemapIndex($sitemapContent);  
                if (!empty($sitemaps)) {
                    $filteredSitemaps = array_filter($sitemaps, function($sitemap) {
                        $url = $sitemap['url'];
                        return strpos($url, 'sitemap-posts-post') !== false || 
                               strpos($url, 'post-sitemap') !== false;
                    });
                    
                    if (!empty($filteredSitemaps)) {
                        usort($filteredSitemaps, function($a, $b) {
                            return strtotime($b['lastmod']) - strtotime($a['lastmod']);
                        });
                        
                        return $filteredSitemaps[0]['url'];
                    }
                    
                    usort($sitemaps, function($a, $b) {
                        return strtotime($b['lastmod']) - strtotime($a['lastmod']);
                    });
                    
                    return $sitemaps[0]['url'];
                }
            }
            return $sitemapUrl;
            
        } catch (\Exception $e) {
            return $sitemapUrl;
        }
    }
    
    /**
     * Faz o parse de um sitemap index e retorna array com URLs e lastmod
     */
    private function parseSitemapIndex($sitemapContent)
    {
        $sitemaps = [];
        preg_match_all('/<sitemap>.*?<loc>(.*?)<\/loc>.*?<lastmod>(.*?)<\/lastmod>.*?<\/sitemap>/s', $sitemapContent, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $sitemaps[] = [
                'url' => trim($match[1]),
                'lastmod' => trim($match[2])
            ];
        }
        return $sitemaps;
    }

    private function optimizeContent( $title, $content, $postKeyWords, $postSitemapUrl ) 
    {
        try 
        {
            foreach ( $postKeyWords as $keyword ) 
            {
                if (strlen($keyword) > 3) { 
                    $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';
                    $content = preg_replace($pattern, '<strong>$0</strong>', $content, 1);
                }
            }

            // Verificar se o sitemap é direto ou um index
            $finalSitemapUrl = $this->getFinalSitemapUrl($postSitemapUrl);
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $finalSitemapUrl,
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
            
            $sitemapUrls = null;
            if ($postSitemap && $httpCode === 200) {
                preg_match_all('/<loc>(.*?)<\/loc>/', $postSitemap, $postMatches);
                if (!empty($postMatches[1])) {
                    $sitemapUrls = $postMatches[1];
                }
            }
            
            $linkCount = 0;           
            $titleWords = explode(' ', $title);
            foreach ($titleWords as $word) {
                if ($linkCount >= 3) break;
                $wordFormat = removeAccents(strtolower($word));
                if (empty($wordFormat) || strlen($wordFormat) <= 5 || !$sitemapUrls) continue;
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
            //dd($e);
        }
    }

}