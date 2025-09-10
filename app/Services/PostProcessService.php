<?php

namespace App\Services;

use App\Models\WebsitePostQueue;

class PostProcessService
{
    public function getPostsToProcess( $typeId )
    {
        $websitePostsQueue = WebsitePostQueue::where("status_id",WebsitePostQueue::STATUS_PENDING)->orderBy("id","asc");
        if( $typeId ){
            $websitePostsQueue = $websitePostsQueue->where("type_id",$typeId);
        }
        $websitePostsQueue = $websitePostsQueue->limit(1)->get();

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

        $processedParams = (object) [];
        
        if( $devMode ) {
            $processedParams->title = "Dev";
            $processedParams->description = "Dev";
            $processedParams->content = "Dev";
            $processedParams->seoData = '{"title":"DEV","description":"DEV","keywords":["DEVs","desenvolvimento","programação","desenvolvedor","software"],"focus_keyword":"DEVs"}';
            return $processedParams;
        }

        if( $websitePostQueue->type_id == WebsitePostQueue::TYPE_COPY )
        {
            $processedParams->title       = $postParams->title;
            $processedParams->description = $postParams->description;
            $processedParams->content     = $postParams->content;
        }
        elseif( $websitePostQueue->type_id == WebsitePostQueue::TYPE_REWRITE )
        {
            # Title
            echo "title - ";
            $title = $this->rewriteAi( $postParams->title, 'title' );
            $processedParams->title = substr($title, -1) == '.' ? substr($title, 0, -1) : $title;
            
            # Description
            echo "description - ";
            $processedParams->description = $this->rewriteAi( $postParams->description );
            if (mb_strlen($processedParams->description) > 155) {
                $processedParams->description = mb_substr($processedParams->description, 0, 152) . '...';
            }
            
            # Content
            echo "content - ";
            $processedParams->content = $this->rewriteAi( $postParams->content, 'content' );
            echo "content validation - ";
            $processedParams->content = AuxService::validText( $processedParams->content );
            echo "removendo blocos repetidos - ";
            $processedParams->content = AuxService::removeRepeatedBlocks( $processedParams->content );
        }
        
        # Palavras Chave
        echo "SEO - ";
        $seoData = SeoAiService::optimizeSeo($processedParams->title, $processedParams->description, $processedParams->content );
        $processedParams->seoData = (object) $seoData;

        return $processedParams;
    }
    
    public function rewriteAi( $text, $type=null )
    {
        $result = RewriterAiService::getResultsAi( $text, $type );

        return $result;
    }

}