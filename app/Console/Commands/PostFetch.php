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
            
            try
            {
                $postNew = PostFetchService::fetchNewPost( $source );
                echo count($postNew);

                foreach( $postNew as $postData )
                {
                    echo "$postData->id = ";
                    $sourceQueue = SourceQueue::register( $source, $postData);

                    if( $sourceQueue ) 
                    {
                        echo "OK - ";
                        PostFetchService::getPostData( $sourceQueue->id );
                        echo "postData OK \n";
                    } else {
                        echo "Já Existe! \n";
                    }
                }
            }
            catch(\Exception $err)
            {
                echo("Error PostFetch: ".errorMessage($err));
            }
        }
        
        echo "\n";

        return true;
    }

    
}
