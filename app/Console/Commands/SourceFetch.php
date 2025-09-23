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

        $sources = Source::where("status_id", Source::STATUS_ACTIVE)->get();

        foreach($sources as $source) 
        {   
            echo "\nSourceId: $source->id - Nome: $source->name \n";
            
            try
            {   
                if ($source->type_id === Source::TYPE_WP) {
                    $postFetchService = new PostFetchService($source);

                    $postNew = $postFetchService->fetchNewPost();
                    echo "Qtd Posts: " . count($postNew) . " - ";

                    foreach ($postNew as $postData) {
                        echo "PostOriginId: $postData->id - Register: ";
                        $sourcePost = SourcePost::register($source, $postData);

                        if ($sourcePost) {
                            echo "OK - ";
                            $postFetchService->getPostData($sourcePost->id);
                            echo "postData OK \n";
                        } else {
                            echo "Já Existe! \n";
                        }
                    }
                } elseif ($source->type_id === Source::TYPE_CUSTOM) {
                    echo "Processando fonte customizada... ";

                    $postFetchService = new PostFetchService($source);
                    $postNew = $postFetchService->fetchNewPost();
                    echo "Qtd Posts: " . count($postNew) . " - ";

                    foreach ($postNew as $postDataEntry) {
                        echo "Endpoint: {$postDataEntry->endpoint} - Register: ";

                        try {
                            $this->postExists($source->id, $postDataEntry->endpoint);
                        } catch (\Exception $e) {
                            echo "Já Existe! \n";
                            continue;
                        }

                        $sourcePost = SourcePost::register($source, $postDataEntry);
                        if ($sourcePost) {
                            echo "OK - ";
                            $postFetchService->getPostData($sourcePost->id);
                            echo "postData OK \n";
                        } else {
                            echo "Já Existe! \n";
                        }
                    }
                } else {
                    echo "Tipo de fonte não suportado, pulando.\n";
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
