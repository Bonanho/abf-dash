<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

use App\Models\Source;
use App\Models\SourcePost;
use App\Services\PostFetchService;
use App\Models\Website;
use App\Models\WebsitePostQueue;

class SourceFetch extends Command
{
    protected $signature = 'source:fetch';

    protected $description = 'Busca materias nos sites fonte e prepara os parametros';

    public function handle() 
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** SourceFetch - " . $printDate . " **********");

        $sources = Source::where("status_id", Source::STATUS_ACTIVE)->get();
        foreach($sources as $source) 
        {   
            echo "\nSourceId: $source->id - Nome: $source->name - Tipo: **".Source::TYPES[$source->type_id]."** \n";
            
            try
            {   
                $postFetchService = new PostFetchService($source);

                $postNew = $postFetchService->fetchNewPost();

                foreach ($postNew as $postData) 
                {
                    if ( $source->type_id==Source::TYPE_WP || $source->type_id==Source::TYPE_CUSTOM ) 
                    {
                        echo "Endpoint: {$postData->endpoint} \nRegister: ";
                        $sourcePost = SourcePost::register($source, $postData);

                        if ($sourcePost) {
                            echo "OK \n";
                            $postFetchService->getPostData($sourcePost->id);
                            echo "PostData OK \n";
                        } else {
                            echo "Já Existe! \n";
                        }
                    } 
                    else {
                        echo "Tipo de fonte não suportado, pulando.\n";
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

    public function postExists($sourceId, $url)
    {
        $exists = SourcePost::where("source_id", $sourceId)->where("endpoint", $url)->count();
        if ($exists) {
            throw new \Exception("Matéria já existe");
        }
    }

}
