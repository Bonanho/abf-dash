<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

use App\Services\PostProcessService;
use App\Models\WebsitePost;
use App\Models\WebsitePostQueue;

class PostProcess extends Command
{
    protected $signature = 'post:process {typeId?}';

    protected $description = 'Pega um registro da fila e reescreve a matéria para um website';

    public function handle()
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** PostProcess - " . $printDate . " **********");

        $typeId  = $this->argument('typeId') ?? false;

        $postProcessService = new PostProcessService();
        $websitePostsQueue = $postProcessService->getPostsToProcess( $typeId );

        foreach( $websitePostsQueue as $wPostQ )
        {
            try
            {  
                $printDate = (new DateTime())->format('H:i:s');
                echo "PostQueueId: ".$wPostQ->id. " = $printDate ";

                $fetchedParameters = $wPostQ->SourcePost->doc;

                $processedParams = $postProcessService->run( $wPostQ->id );
                
                if( !$processedParams )
                {
                    $wPostQ->status_id = WebsitePostQueue::STATUS_ERROR;
                    $wPostQ->save();
                    continue;
                }

                $websitePost = new WebsitePost();
                $websitePost->website_post_queue_id = $wPostQ->id;
                $websitePost->website_id            = $wPostQ->website_id;
                $websitePost->website_source_id     = $wPostQ->website_source_id;
                $websitePost->source_id             = $wPostQ->source_id;
                $websitePost->source_post_id        = $wPostQ->source_post_id;
                
                $websitePost->post_title            = $processedParams->title;
                $websitePost->post_description      = $processedParams->description;
                $websitePost->post_content          = $processedParams->content;
                $websitePost->seo_data              = $processedParams->seoData;
                
                $websitePost->post_image            = $fetchedParameters->image;
                $websitePost->post_image_caption    = $fetchedParameters->image_caption;
                $websitePost->post_category         = $fetchedParameters->category;
                $websitePost->url_original          = $fetchedParameters->url_original;

                $websitePost->save();

                $wPostQ->setStatus( WebsitePostQueue::STATUS_DONE );

                $printDate = (new DateTime())->format('H:i:s');
                echo " OK $printDate \n";
            }
            catch(\Exception $err)
            {
                echo("Error PostProcess: " . errorMessage($err) . "\n\n");
            }
        }

        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** PostProcess - " . $printDate . " **********");
    }

}
