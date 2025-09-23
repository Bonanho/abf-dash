<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

use App\Models\Source;
use App\Models\SourcePost;
use App\Services\CustomFetchService;

class SourceFetchCustom extends Command
{
    protected $signature = 'source:custom';

    protected $description = 'Busca materias nos sites fonte e prepara os parametros';

    public function handle() 
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** SourceFetch - " . $printDate . " **********");

        // $sources = Source::getSourcesToFetchPosts();
        $sources = Source::whereIn("id",[1, ])->get();
        //$sources = Source::whereIn("id",[1,2,4,5,7,14])->get();

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

                $node = $crawler->filter($source->template->homeNew)->first();
                if ($node->count()) {
                    $newPostUrl   = $node->attr('href');
                    if (strpos($newPostUrl, 'http') !== 0) {
                        // $newPostUrl = $this->baseUrl . $newPostUrl;
                        $newPostUrl = rtrim($baseUrl, '/') . '/' . ltrim($newPostUrl, '/');
                        echo " ajusout-URL ";
                    }
                }

                $this->postExists( $source->id, $newPostUrl );

                $crawlerData = Http::get($newPostUrl);
                if ($crawlerData->ok()) 
                {
                    $crawler = new Crawler($crawlerData->body(), $newPostUrl);

                    $customFetch = new CustomFetchService( $source );
                    $postData = $customFetch->fetchSource( $crawler );

                    if (empty(trim($postData->content))) {
                        echo "Conteúdo insuficiente, pulando registro.\n";
                        continue;
                    }

                    $postData->url_original = $newPostUrl;
                    
                    $sourcePost = SourcePost::registerCustom( $source, $postData);
                    if( $sourcePost ){
                        echo "OK \n";
                    } else {
                        echo "já existe \n";
                    }
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

    public function postExists( $sourceId, $url )
    {
        $exists = SourcePost::where("source_id",$sourceId)->where("endpoint",$url)->count();
        if( $exists ){
            throw new \Exception("Matéria já existe");
        }
    }
}