<?php

namespace App\Services;

use App\Models\WebsitePostQueue;

class PostRewriteService
{
    public function getPostsToRewrite()
    {
        $websitePostsQueue = WebsitePostQueue::where("status_id",WebsitePostQueue::STATUS_PENDING)->orderBy("id","asc")->get();

        return $websitePostsQueue;
    }

    public function run( $wpqId, $devMode=false )
    {
        $websitePostQueue = WebsitePostQueue::find( $wpqId );
        if( $websitePostQueue->status_id != WebsitePostQueue::STATUS_PENDING || !$websitePostQueue->SourcePost ){
            return false;
        }

        $websitePostQueue->setStatus( WebsitePostQueue::STATUS_PROCESSING );

        $postParams = $websitePostQueue->SourcePost->doc;

        $rewritedParams = (object) [];
        
        if( $devMode ) {
            $rewritedParams->title = "Dev";
            $rewritedParams->description = "Dev";
            $rewritedParams->content = "Dev";
            $rewritedParams->seoData = '{"title":"DEV","description":"DEV","keywords":["DEVs","desenvolvimento","programação","desenvolvedor","software"],"focus_keyword":"DEVs"}';
        }

        # Title
        echo "title - ";
        $title = $this->rewriteAi( $postParams->title, 'title' );
        $rewritedParams->title = substr($title, -1) == '.' ? substr($title, 0, -1) : $title;
        
        # Description
        echo "description - ";
        $rewritedParams->description = $this->rewriteAi( $postParams->description );
        if (mb_strlen($rewritedParams->description) > 155) {
            $rewritedParams->description = mb_substr($rewritedParams->description, 0, 152) . '...';
        }
        
        # Content
        echo "content - ";
        $rewritedParams->content = $this->rewriteAi( $postParams->content, 'content' );
        echo "content validation - ";
        $rewritedParams->content = AuxService::validText( $rewritedParams->content );

        # Palavras Chave
        echo "SEO - ";
        $seoData = SeoAiService::optimizeSeo($rewritedParams->title, $rewritedParams->description, $rewritedParams->content );
        $rewritedParams->seoData = (object) $seoData;

        return $rewritedParams;
    }
    
    public function rewriteAi( $text, $type=null )
    {
        $result = RewriterAiService::getResultsAi( $text, $type );

        return $result;
    }

}