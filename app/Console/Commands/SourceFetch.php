<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

use App\Models\Source;
use App\Models\SourcePost;
use App\Services\PostFetchService;

class SourceFetch extends Command
{
    protected $signature = 'source:fetch';

    protected $description = 'Busca materias nos sites fonte e prepara os parametros';

    public function handle() 
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** SourceFetch - " . $printDate . " **********");

        $sources = Source::getSourcesToFetchPosts();

        foreach($sources as $source) 
        {   
            echo "\n$source->name \n";
            
            try
            {   
                $postFetchService = new PostFetchService( $source );

                $postNew = $postFetchService->fetchNewPost();
                echo "Qtd Posts: " . count($postNew)." - ";

                foreach( $postNew as $postData )
                {
                    echo "PostOriginId: $postData->id - Register: ";
                    $sourcePost = SourcePost::register( $source, $postData);
                    
                    if( $sourcePost ) 
                    {
                        echo "OK - ";
                        $postFetchService->getPostData( $sourcePost->id );
                        echo "postData OK \n";
                    } else {
                        echo "Já Existe! \n";
                    }
                }
                
            }
            catch(\Exception $err)
            {
                echo("Error PostFetch: " . errorMessage($err) . "\n\n");
            }
        }
        
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("\n********** SourceFetch - FIM - " . $printDate . " **********\n");
    }

}
