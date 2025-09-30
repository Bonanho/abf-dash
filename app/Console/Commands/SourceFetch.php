<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

use App\Models\Source;
use App\Models\SourcePost;
use App\Models\Website;
use App\Services\PostFetchService;
use App\Models\WebsiteSource;

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
            
            $hasWebsiteSource = WebsiteSource::where("source_id",$source->id)->where("status_id",WebsiteSource::STATUS_ACTIVE)->count();
            if($hasWebsiteSource==0){
                echo "Sem associação a website! \n";
                continue;
            }

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
                            
                            if( !$this->postValidation($sourcePost->id) ) {
                                echo "Matéria nao contem nenhuma palavra chave \n";
                                continue;
                            }

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

    public function postValidation( $sourcePostId )
    {
        ### CQCS ###
        $website = Website::find(1);
        if( $website->Company->name == "CQCS" )
        {
            $sourcePost = SourcePost::find($sourcePostId);

            $count = 0;
            $keywords = $website->doc->keywords;
            
            $objKeywords = (object) [];
            $objKeywords->title = $objKeywords->description = $objKeywords->content = "";

            foreach ($keywords as $keyword) 
            {
                $keyword = mb_strtolower($keyword);
                if (stripos(mb_strtolower($sourcePost->doc->title), $keyword) !== false) {
                    $objKeywords->title .= $keyword.", ";
                    $count++;
                }
                if (stripos(mb_strtolower($sourcePost->doc->description), $keyword) !== false) {
                    $objKeywords->description .= $keyword.", ";
                    $count++;
                }
                if (stripos(mb_strtolower($sourcePost->doc->content), $keyword) !== false) {
                    $objKeywords->content .= $keyword.", ";
                    $count++;
                }
            }
            
            if( $count == 0){
                $sourcePost->status_id = SourcePost::STATUS_ERROR;
                $sourcePost->error = "Matéria não contém nenhuma palavra chave";
                $sourcePost->save();

                return false;
            } 
            else {
                $sourcePost->error = $objKeywords;
                $sourcePost->save();

                 return true;
            }
        };
    }

}