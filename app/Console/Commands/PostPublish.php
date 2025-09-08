<?php

namespace App\Console\Commands;

use App\Models\WebsitePost;
use App\Services\PostPublishService;
use Illuminate\Console\Command;
use DateTime;

class PostPublish extends Command
{
    protected $signature = 'post:publish {--limit}';

    protected $description = 'Publica a matéria no website';

    public function handle()
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** PostPublish - " . $printDate . " **********");

        $limit = $this->option('limit');

        $websitePosts = WebsitePost::where("status_id", WebsitePost::STATUS_PENDING)->with("Website")->with("Source");
        if( $limit ) {
            $websitePosts->limit(1);
        }
        $websitePosts = $websitePosts->get();

        foreach($websitePosts as $wPost) 
        {
            echo "\n".$wPost->Website->name." / ".$wPost->Source->name." - WebsitePostId: ".$wPost->id."\n";
            try
            {   
                $postPublishService = new PostPublishService( $wPost );

                $postPublishService->run();
            }
            catch(\Exception $err)
            {
                echo("Catch->PostPublish: " . errorMessage($err) . "\n\n");
            }
        }
        
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("\n********** PostPublish - FIM - " . $printDate . " **********\n");
    }
    
}