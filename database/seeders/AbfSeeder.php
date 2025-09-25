<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DateTime;

use App\Models\AuxCategory;
use App\Models\AuxNetwork;

use App\Models\Company;
use App\Models\Source;
use App\Models\Website;
use App\Models\WebsiteSource;

class AbfSeeder extends Seeder
{   
    public function run()
    {   
        $date = (new DateTime())->format("Y-m-d H:i:s");
        
        Company::insert([
            ['id'=>1, 'name'=>'ABF', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Careca CORP', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        AuxNetwork::insert([
            ['id'=>1, 'name'=>'MGID', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'GAM', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Google ADS', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        AuxCategory::insert([
            ['id'=>1, 'name'=>'Notícias', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Finanças', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Tecnologia', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>4, 'name'=>'Entreterimento', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>5, 'name'=>'Esportes', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>6, 'name'=>'Saúde', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>7, 'name'=>'Viagens', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>8, 'name'=>'Seguros', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>9, 'name'=>'Astrologia', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        # Websites
        $this->setWebsite(1, 1, "Alerta Jornal",  "https://alertajornal.com.br",   'redacao', 'WpProject$2025?!'); // [\"Brasil de Fato\", \"FDR\"]
        $this->setWebsite(1, 1, "Bona News",      "https://bonanews.com.br",       'redacao', 'WpProject$2025?!'); // [\"Poder 360\", \"Jovem Pan\"]
        $this->setWebsite(1, 2, "Invest Agora",   "https://investagora.com.br",    'redacao', 'WpProject$2025?!'); // [\"Seu Dinheiro\", \"Cartão a Crédito\"]
        $this->setWebsite(1, 2, "Papo Invest",    "https://papoinvest.com.br",     'redacao', 'WpProject$2025?!'); // [\"Suno\", \"Empiricus\", \"InfoMoney\"]
        $this->setWebsite(1, 3, "Techzando",      "https://techzando.com.br",      'redacao', 'WpProject$2025?!'); // [\"Adrenaline\"]
        $this->setWebsite(2, 2, "Zé News AI",     "https://zenewsai.com.br",       'JoaoPadilha', 'kg1jJXM!65cRCudarR7dTfJ'); // [\"Jovem Pan\"]
        $this->setWebsite(2, 2, "Clique Fatos",   "https://cliquefatos.com.br",    'RedacaoCliqueFatos', 'a^jIG6C%ehEl1st2CIxTI*z'); // [\"CNN Brasil\", \"Personare\", \"Carta Capital\", \"O Antagonista\"]
        
        Source::insert([
            ['name'=>'CNN Brasil',          'url'=>'https://www.cnnbrasil.com.br',          'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Jovem Pan',           'url'=>'https://jovempan.com.br',               'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Poder 360',           'url'=>'https://www.poder360.com.br',           'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Personare',           'url'=>'https://admin-cms.personare.com.br',    'category_id'=>9, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Carta Capital',       'url'=>'https://www.cartacapital.com.br',       'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Brasil de Fato',      'url'=>'https://www.brasildefato.com.br',       'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Adrenaline',          'url'=>'https://www.adrenaline.com.br',         'category_id'=>3, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'FDR',                 'url'=>'https://fdr.com.br',                    'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'O Antagonista',       'url'=>'https://oantagonista.com.br',           'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Suno',                'url'=>'https://www.suno.com.br',               'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Empiricus',           'url'=>'https://www.empiricus.com.br',          'category_id'=>2, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'InfoMoney',           'url'=>'https://www.infomoney.com.br',          'category_id'=>2, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Seu Dinheiro',        'url'=>'https://www.seudinheiro.com',           'category_id'=>2, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['name'=>'Cartão a Crédito',    'url'=>'https://www.cartaoacredito.com',        'category_id'=>2, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        

        $postStatus = '{"rewrite": 1, "defaultPostStatus": "publish"}';
        WebsiteSource::insert([
            ['website_id'=>1, 'source_id'=>'1', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>1, 'source_id'=>'2', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>1, 'source_id'=>'3', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],

            ['website_id'=>2, 'source_id'=>'1', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>2, 'source_id'=>'2', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>2, 'source_id'=>'3', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],

            ['website_id'=>3, 'source_id'=>'11', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>3, 'source_id'=>'12', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>3, 'source_id'=>'13', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>3, 'source_id'=>'14', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            
            ['website_id'=>4, 'source_id'=>'11', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>4, 'source_id'=>'12', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>4, 'source_id'=>'13', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>4, 'source_id'=>'14', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            
            ['website_id'=>5, 'source_id'=>'7', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
        ]);

    }

    public function setWebsite( $company, $category, $name, $url, $user, $pass )
    {   
        $date = (new DateTime())->format("Y-m-d H:i:s");

         Website::insert([
            [
                'company_id'=>$company, 'name'=>$name, 'url'=>$url, 'category_id'=>$category, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date,
                'config'=>'{"siteMap":"wp-sitemap.xml","wpUser":"'.$user.'","wpPass":"'.$pass.'"}' 
            ],
        ]);
    }

}