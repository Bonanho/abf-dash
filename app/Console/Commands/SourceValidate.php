<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use GuzzleHttp\Client;

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
        // $sources = Source::where("id", 23 )->get();

        foreach($sources as $source) 
        {
            echo "\n############################################################################\n";
            echo "\n$source->name = ";
            
            ##########################
            ### Testa se site é WP ###
            if ( $this->isWordPress($source->url) ) {
                echo "$source->url  WP = ✅\n";
                $source->type_id = Source::TYPE_CUSTOM;
            } else {
                echo "$source->url  WP = ❌\n";
                $source->type_id = Source::TYPE_CUSTOM;
            }
            $source->save();
            ##########################
            echo "_____________________________________________________________________________\nFeed: ";
            $this->testFeed( $source->url );
            echo "_____________________________________________________________________________\nSiteMap: ";
            $this->testSitemap( $source->url );
            echo "_____________________________________________________________________________\n";

            ##############################
            ### Testa se funciona hoje ###
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
            ##############################
        }
        
        $printDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->line("\n********** SourceValidate - FIM - " . $printDate . " **********\n");
    }



    function isWordPress($url)
    {
        $client = new Client([
            'timeout' => 10,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0',
            ],
        ]);

        try {
            $response = $client->get($url);

            $html = (string) $response->getBody();
            $headers = $response->getHeaders();

            $htmlLower = strtolower($html);

            // 1. meta generator
            if (preg_match('/<meta name="generator" content="wordpress/i', $html)) {
                return true;
            }

            // 2. caminhos comuns
            if (strpos($htmlLower, 'wp-content') !== false ||
                strpos($htmlLower, 'wp-includes') !== false ||
                strpos($htmlLower, 'wp-json') !== false) {
                return true;
            }

            // 3. headers HTTP
            foreach ($headers as $key => $values) {
                if (stripos($key, 'wordpress') !== false ||
                    stripos(implode(' ', $values), 'wordpress') !== false) {
                    return true;
                }
            }

            // 4. (extra) testa rota /wp-json/
            try {
                $jsonResponse = $client->get(rtrim($url, '/') . '/wp-json/', [
                    'http_errors' => false
                ]);
                if ($jsonResponse->getStatusCode() === 200) {
                    return true;
                }
            } catch (\Exception $e) {
                // ignora erro
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    
    function testFeed($url)
    {
        $client = new Client([
            'timeout' => 10,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0',
            ],
        ]);

        $feedUrl = rtrim($url, '/') . '/feed/';

        try {
            $response = $client->get($feedUrl, ['http_errors' => false]);

            if ($response->getStatusCode() === 200) {
                $xml = simplexml_load_string((string) $response->getBody());
                if ($xml && isset($xml->channel->item[0])) {
                    $item = $xml->channel->item[0];
                    echo "✅ RSS encontrado em $feedUrl\n";
                    echo "Última publicação: {$item->title} ({$item->pubDate})\n";
                } else {
                    echo "⚠️ RSS existe mas não consegui ler os itens.\n";
                }
            } else {
                echo "❌ RSS não encontrado em $feedUrl\n";
            }
        } catch (\Exception $e) {
            echo "❌ Erro ao testar RSS: " . $e->getMessage() . "\n";
        }
    }


    function testSitemap($url)
    {
        $client = new Client([
            'timeout' => 10,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0',
            ],
        ]);

        $sitemapUrl = rtrim($url, '/') . '/sitemap.xml';

        try {
            $response = $client->get($sitemapUrl, ['http_errors' => false]);

            if ($response->getStatusCode() === 200) {
                $xml = simplexml_load_string((string) $response->getBody());
                if ($xml && isset($xml->url[0])) {
                    $first = $xml->url[0];
                    $loc = isset($first->loc) ? $first->loc : 'sem URL';
                    $lastmod = isset($first->lastmod) ? $first->lastmod : 'sem data';
                    echo "✅ Sitemap encontrado em $sitemapUrl\n";
                    echo "Primeira URL no sitemap: $loc (lastmod: $lastmod)\n";
                } else {
                    echo "⚠️ Sitemap existe mas não consegui ler as URLs.\n";
                }
            } else {
                echo "❌ Sitemap não encontrado em $sitemapUrl\n";
            }
        } catch (\Exception $e) {
            echo "❌ Erro ao testar Sitemap: " . $e->getMessage() . "\n";
        }
    }

}




