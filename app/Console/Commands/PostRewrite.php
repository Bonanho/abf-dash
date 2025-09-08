<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

use App\Services\PostRewriteService;
use App\Models\WebsitePost;
use App\Models\WebsitePostQueue;

class PostRewrite extends Command
{
    protected $signature = 'post:rewrite {--dev}';

    protected $description = 'Pega um registro da fila e reescreve a matéria para um website';

    public function handle()
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** PostRewrite - " . $printDate . " **********");

        $devMode = $this->option('dev');

        $postRewriteService = new PostRewriteService();

        $websitePostsQueue = $postRewriteService->getPostsToRewrite();

        foreach( $websitePostsQueue as $wPostQ )
        {
            $printDate = (new DateTime())->format('H:i:s');
            echo "PostQueueId: ".$wPostQ->id. " = $printDate ";

            $fetchedParameters = $wPostQ->SourcePost->doc;

            $rewritedParams = $postRewriteService->run( $wPostQ->id, $devMode );

            $websitePost = new WebsitePost();
            $websitePost->website_post_queue_id = $wPostQ->id;
            $websitePost->website_id            = $wPostQ->website_id;
            $websitePost->website_source_id     = $wPostQ->website_source_id;
            $websitePost->source_id             = $wPostQ->source_id;
            $websitePost->source_post_id        = $wPostQ->source_post_id;
            $websitePost->post_title            = $rewritedParams->title;
            $websitePost->post_description      = $rewritedParams->description;
            $websitePost->post_content          = $rewritedParams->content;
            $websitePost->seo_data              = $rewritedParams->seoData;
            $websitePost->post_image            = $fetchedParameters->image;
            $websitePost->post_image_caption    = $fetchedParameters->image_caption;
            $websitePost->post_category         = $fetchedParameters->category;
            $websitePost->url_original          = $fetchedParameters->url_original;
            $websitePost->save();

            $wPostQ->setStatus( WebsitePostQueue::STATUS_DONE );

            $printDate = (new DateTime())->format('H:i:s');
            echo " OK $printDate \n";
        }

        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** PostRewrite - " . $printDate . " **********");
    }

}
