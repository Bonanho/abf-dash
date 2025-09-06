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

        Website::insert([
            ['id'=>1, 'company_id'=>1, 'name'=>'Cqcs Site', 'url'=>'cqcssite.com.br', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        $this->setSource('Estadao',                     2, "https://www.estadao.com.br" );
        $this->setSource('Valor econômico ',            3, "https://valor.globo.com" );
        $this->setSource('Sonho Seguro',                1, "https://www.sonhoseguro.com.br" );
        $this->setSource('Diario oficial',              1, 'http://www.seguros.inf.br' );
        $this->setSource('Susep',                       1, 'http://www.susep.gov.br' );
        $this->setSource('Fenacor',                     1, 'http://www.fenacor.com.br' );
        $this->setSource('ANS',                         1, 'http://www.ans.gov.br' );
        $this->setSource('Revista Apólice',             1, 'http://revistaapolice.com.br' );
        $this->setSource('Exame',                       1, 'http://exame.abril.com.br' );
        $this->setSource('Época',                       1, 'http://epoca.globo.com' );
        $this->setSource('Estadão',                     1, 'http://www.estadao.com.br' );
        $this->setSource('Folha',                       1, 'http://www.folha.com.br' );
        $this->setSource('O Globo',                     1, 'http://oglobo.globo.com' );
        $this->setSource('Jornal do Comércio',          1, 'http://jcrs.uol.com.br' );
        $this->setSource('Correio do Povo',             1, 'http://www.correiodopovo.com.br' );
        $this->setSource('Diário Oficial da União',     1, 'http://portal.in.gov.br' );
        $this->setSource('Sincor-SP',                   1, 'http://www.sincor.org.br' );
        $this->setSource('SindMG',                      1, 'https://sindsegmd.com.br' );
        $this->setSource('Risco Seguro Brasil',         1, 'http://riscosegurobrasil.com' );
        $this->setSource('Monitor Mercantil',           1, 'http://www.monitormercantil.com.br' );
        $this->setSource('Revista Cobertura',           1, 'http://www.revistacobertura.com.br' );
        $this->setSource('Escola Nacional de Seguros',  1, 'http://www.funenseg.org.br' );
        $this->setSource('Capitólio',                   1, 'https://capitolio.com.br' );
        $this->setSource('Sindseg RS',                  1, 'http://www.sindsegrs.com.br' );
        $this->setSource('Sincorrs',                    1, 'http://www.sincorrs.com' );
        $this->setSource('Seguros Inf',                 1, 'http://www.seguros.inf.br' );
        $this->setSource('Sindseg-SP',                  1, 'http://www.sindsegsp.org.br/site' );
        $this->setSource('Zero Hora',                   1, 'http://zh.clicrbs.com.br/rs' );
        $this->setSource('Câmara dos Deputados',        1, 'http://www2.camara.leg.br/camaranoticias' );
        $this->setSource('CNseg',                       1, 'http://www.cnseg.org.br/cnseg/home.html' );
        $this->setSource('Sulamerica',                  1, 'http://sulamerica.comunique-se.com.br/sulamerica' );
        $this->setSource('Praque Seguro',               1, 'http://www.praqueseguro.com.br/2018/01/o-chato-do-corretor.html' );
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
            ['website_id'=>1, 'source_id'=>$source->id, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
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