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
        ]);

        # Websites
        $this->setWebsite( 1, 1, "Alerta Jornal",  "alertajornal.com.br"    );
        $this->setWebsite( 1, 1, "Bona News",      "bonanews.com.br"        );
        $this->setWebsite( 1, 2, "Invest Agora",   "investagora.com.br"     );
        $this->setWebsite( 1, 2, "Papo Invest",    "papoinvest.com.br"      );
        $this->setWebsite( 1, 3, "Techzando",      "techzando.com.br"       );
        
        Source::insert([
            ['id'=>1, 'name'=>'CNN',       'url'=>'https://cnnbrasil.com.br',   'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Jovem Pan', 'url'=>'https://jovempan.com.br',    'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Poder 360', 'url'=>'https://www.poder360.com.br', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        $postStatus = '{"defaultPostStatus":"publish"}';
        WebsiteSource::insert([
            ['website_id'=>1, 'source_id'=>'1', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>1, 'source_id'=>'2', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>2, 'source_id'=>'1', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>2, 'source_id'=>'3', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>3, 'source_id'=>'1', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>2, 'source_id'=>'2', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
            ['website_id'=>2, 'source_id'=>'3', 'status_id'=>1, 'doc'=> $postStatus, 'created_at'=>$date, 'updated_at'=>$date],
        ]);

    }

    public function setWebsite( $companyId, $category, $name, $url )
    {   
        $date = (new DateTime())->format("Y-m-d H:i:s");

         Website::insert([
            ['company_id'=>$companyId, 'name'=>$name, 'url'=>$url, 'category_id'=>$category, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
        ]);
    }

}