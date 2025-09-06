<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

use App\Models\Source;
use App\Models\SourcePost;
use App\Services\PostFetchService;

class SourceValidate extends Command
{
    protected $signature = 'source:validate';

    protected $description = 'Valida se sources está funcional para baixar conteúdos';

    public function handle() 
    {
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("********** SourceValidate - " . $printDate . " **********");

        $sources = Source::whereIn("status_id", [Source::STATUS_PENDING, Source::STATUS_INVALID] )->get();

        foreach($sources as $source) 
        {   
            echo "\n$source->name = ";
            
            try
            {   
                $postFetchService = new PostFetchService( $source );

                $result = $postFetchService->fetchValidation();
                
                $result = ( $result == Source::STATUS_ACTIVE ) ? "OK" : "Inválido!";

                echo $result . "\n";
            }
            catch(\Exception $err)
            {
                echo("Erro na validação de fonte: " . errorMessage($err) . "\n\n");
            }

        }
        
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("\n********** SourceValidate - FIM - " . $printDate . " **********\n");
    }

}
