<?php

namespace App\Console\Commands;

use App\Models\SourcePost;
use Illuminate\Console\Command;
use DateTime;

use App\Models\Website;
use App\Models\WebsitePostQueue;

class PostQueue extends Command
{
    protected $signature = 'post:queue';

    protected $description = 'Baseado nas regras de website e posts de fontes disponiveis faz a fila para reescrita';

    public function handle()
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** PostQueue - " . $printDate . " **********");

        $websites = Website::where("status_id",Website::STATUS_ACTIVE)->get();
        
        # Websites
        foreach( $websites as $website )
        {
            echo "\n- $website->name \n";
            
            $sourcePosts = null;

            if( $website->Sources )
            {
                # last Posts by website
                $cutDate = (new DateTime())->modify("-48 hour");
                $lastPostsId = WebsitePostQueue::where("website_id",$website->id)->where("created_at", ">=", $cutDate)->orderBy("id","desc")->pluck("source_post_id");

                # Source Posts
                $cutDate = (new DateTime())->modify("-4 hour");
                $sourcePosts = SourcePost::where("created_at", ">=", $cutDate)->where("status_id",SourcePost::STATUS_DONE)
                    ->whereIn("source_id",$website->Sources->pluck("source_id"))
                    ->whereNotIn("id",$lastPostsId)
                    ->select("id","source_id","created_at","doc->title as title")->get();

                $sourcePosts = $sourcePosts->groupBy("source_id");
            
                # Websites - Sources
                foreach( $website->Sources as $wSource )
                {
                    $source = $wSource->Source;
                    echo "   " . $source->name . " - ";
                    
                    if( isset($sourcePosts[$source->id]) )
                    {
                        $avaliablePosts = $sourcePosts[$source->id];
                    
                        $selectdPost = $avaliablePosts->random();

                        $wPostQ = new WebsitePostQueue();
                        $wPostQ->website_id         = $website->id;
                        $wPostQ->website_source_id  = $wSource->id;
                        $wPostQ->source_id          = $source->id;
                        $wPostQ->source_post_id     = $selectdPost->id;
                        $wPostQ->type_id            = $wPostQ->defineType();

                        $wPostQ->save();

                        echo $selectdPost->id;
                    } 

                    echo "\n";
                }
            };
            
        }

        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("\n********** PostQueue - FIM - " . $printDate . " **********\n");
    }
}
