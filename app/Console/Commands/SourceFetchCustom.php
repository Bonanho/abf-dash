<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

use App\Models\Source;
use App\Models\SourcePost;
use App\Services\CustomFetchService;
use App\Services\PostFetchService;

class SourceFetch extends Command
{
    protected $signature = 'source:custom';

    protected $description = 'Busca materias nos sites fonte e prepara os parametros';

    public function handle() 
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** SourceFetch - " . $printDate . " **********");

        // $sources = Source::getSourcesToFetchPosts();
        $sources = Source::whereIn("id",[2,4,14])->get();

        foreach( $sources as $source )
        {
            echo "\nSourceId: $source->id - Nome: $source->name = ";

            try
            {  
                $baseUrl = $source->url;
                $response = Http::get($baseUrl);
                
                if (!$response->ok()) {
                    $this->error("Erro ao acessar {$baseUrl}: " . $response->status());
                }
                
                $crawler = new Crawler($response->body(), $baseUrl);

                $customFetch = new CustomFetchService( $source );
                
                $methodName = "fetchSource_".$source->id;
                $postData = $customFetch->$methodName( $crawler );

                $sourcePost = SourcePost::registerCustom( $source, $postData);
                if( $sourcePost ){
                    echo "OK \n";
                } else {
                    echo "já existe \n";
                }
            }
            catch(\Exception $err)
            {
                echo("Error PostFetch CUSTOM: " . errorMessage($err) . "\n\n");
            }
        }
            
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("\n********** SourceFetch - FIM - " . $printDate . " **********\n");
    }

}