<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DateTime;

use App\Models\AuxCategory;

use App\Models\Company;
use App\Models\Source;
use App\Models\Website;
use App\Models\WebsiteSource;

class CqcsSeeder extends Seeder
{   
    public function run()
    {   
        $date = (new DateTime())->format("Y-m-d H:i:s");
        
        Company::insert([
            ['id'=>1, 'name'=>'CQCS', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        AuxCategory::insert([
            ['id'=>1, 'name'=>'Seguros', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Notícias', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Finanças', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        // ['id'=>1, 'company_id'=>1, 'name'=>'Cqcs Site', 'url'=>'https://wp-base.loc/', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date,'config'=>'{"siteMap":"wp-sitemap.xml","wpUser":"Bonanho","wpPass":"BonaWp2025$"}'],
        Website::insert([
            ['id'=>1, 'company_id'=>1, 'name'=>'Cqcs Site', 'url'=>'https://g.mediagrumft.com', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date,
            'config'=>'{"siteMap":"wp-sitemap.xml","wpUser":"cqcs@mediagrumft.com","wpPass":"AfXS!0re2ZN^m6R@F$qFyrCt"}'],
        ]);

        $this->setSource('Estadao',                     2, "https://www.estadao.com.br" );
        $this->setSource('Valor econômico ',            3, "https://valor.globo.com" );
        $this->setSource('Sonho Seguro',                1, "https://www.sonhoseguro.com.br" );
        $this->setSource('Seguros Inf',                 1, 'https://www.seguros.inf.br' );
        $this->setSource('Susep',                       1, 'https://www.susep.gov.br' );
        $this->setSource('ANS',                         1, 'https://www.ans.gov.br' );
        $this->setSource('Revista Apólice',             1, 'https://revistaapolice.com.br' );
        $this->setSource('Exame',                       1, 'https://exame.com/' );
        $this->setSource('Época',                       1, 'https://epoca.globo.com' );
        $this->setSource('Estadão',                     1, 'https://www.estadao.com.br' );
        $this->setSource('Folha',                       1, 'https://www.folha.com.br' );
        $this->setSource('O Globo',                     1, 'https://oglobo.globo.com' );
        $this->setSource('Jornal do Comércio',          1, 'https://jcrs.uol.com.br' );
        $this->setSource('Correio do Povo',             1, 'https://www.correiodopovo.com.br' );
        $this->setSource('Diário Oficial da União',     1, 'https://portal.in.gov.br' );
        $this->setSource('Sincor-SP',                   1, 'https://www.sincor.org.br' );
        $this->setSource('SindMG',                      1, 'https://sindsegmd.com.br' );
        $this->setSource('Risco Seguro Brasil',         1, 'https://riscosegurobrasil.com' );
        $this->setSource('Monitor Mercantil',           1, 'https://www.monitormercantil.com.br' );
        $this->setSource('Revista Cobertura',           1, 'https://www.revistacobertura.com.br' );
        $this->setSource('Escola Nacional de Seguros',  1, 'https://www.funenseg.org.br' );
        $this->setSource('Capitólio',                   1, 'https://capitolio.com.br' );
        $this->setSource('Sindseg RS',                  1, 'https://www.sindsegrs.com.br' );
        $this->setSource('Sincorrs',                    1, 'https://www.sincorrs.com' );
        $this->setSource('Seguros Inf',                 1, 'https://www.seguros.inf.br' );
        $this->setSource('Sindseg-SP',                  1, 'https://www.sindsegsp.org.br/site' );
        $this->setSource('Zero Hora',                   1, 'https://zh.clicrbs.com.br/rs' );
        $this->setSource('Câmara dos Deputados',        1, 'https://www2.camara.leg.br/camaranoticias' );
        $this->setSource('CNseg',                       1, 'https://www.cnseg.org.br/cnseg/home.html' );
        $this->setSource('Sulamerica',                  1, 'https://sulamerica.comunique-se.com.br/sulamerica' );
    }

    public function setSource( $name, $category="1", $url )
    {   
        $date = (new DateTime())->format("Y-m-d H:i:s");

        $source = new Source();
        $source->name        = $name;
        $source->url         = $url;
        $source->category_id = $category;
        $source->save();
        
        WebsiteSource::insert([
            ['website_id'=>1, 'source_id'=>$source->id, 'status_id'=>1, 'doc'=> '{"defaultPostStatus":"publish"}', 'created_at'=>$date, 'updated_at'=>$date],
        ]);
    }
}

// Sites e palavras que costumamos clipar:
// Seguem palavras e sites para clipar:
// Mercado de seguro
// Mercado segurador
// setor de seguros
// setor segurador
// Susep
// Seguro
// Seguros
// Segurado
// Segurados
// Detran
// Seguro Saude
// Seguradora
// Seguradoras
// DPVAT
// Sinistro
// Apolice
// Golpe do Seguro
// Corretor
// PrevidÍncia privada
// Seguro pirata