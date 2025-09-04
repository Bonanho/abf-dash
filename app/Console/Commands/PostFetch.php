<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Source;
use App\Models\SourceQueue;
use App\Services\PostFetchService;

class PostFetch extends Command
{
    protected $signature = 'post:fetch';

    protected $description = 'Command description';

    public function handle() 
    {
        $printDate = (new \DateTime())->format('Y-m-d H');
        $this->line("********** PostFetch - " . $printDate . " **********");

        $sources = Source::getSourcesFetch();

        foreach($sources as $source) 
        {   
            echo "\n$source->name \n";
            $postFetchService = new PostFetchService( $source );
            $sourceQueue = SourceQueue::find(3);
            $postFetchService->getPostData( $sourceQueue->id );
            dd("FIM");
            try
            {   
                $postFetchService = new PostFetchService( $source );

                $postNew = $postFetchService->fetchNewPost();
                echo count($postNew);

                foreach( $postNew as $postData )
                {
                    echo "$postData->id = ";
                    $sourceQueue = SourceQueue::register( $source, $postData);

                    if( $sourceQueue ) 
                    {
                        echo "OK - ";
                        $postFetchService->getPostData( $sourceQueue->id );
                        echo "postData OK \n";
                    } else {
                        echo "Já Existe! \n";
                    }
                }
                dd("FIM");
            }
            catch(\Exception $err)
            {
                echo("Error PostFetch: " . errorMessage($err) . "\n\n");
            }
        }
        
        echo "\n";
    }

}
