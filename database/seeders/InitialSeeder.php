<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DateTime;

use App\Models\User;
use App\Models\AuxCategory;
use App\Models\AuxNetwork;

use App\Models\Company;
use App\Models\Source;
use App\Models\Website;

class InitialSeeder extends Seeder
{   
    public function run()
    {   
        $date = (new DateTime())->format("Y-m-d H:i:s");
        
        // $password = Hash::make("abf@123");
        // User::insert([
        //     ['id'=>1, 'name'=>'SuperUser', 'profile_id'=>1, 'email'=>'super@abf.com', 'password'=>$password, 'status_id'=>1, 'email_verified_at'=>$date, 'created_at'=>$date, 'updated_at'=>$date],
        // ]);

        Company::insert([
            ['id'=>1, 'name'=>'ABF', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        AuxCategory::insert([
            ['id'=>1, 'name'=>'Notícias', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Finanças', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Tecnologia', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>4, 'name'=>'Entreterimento', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>5, 'name'=>'Esportes', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>6, 'name'=>'Saúde', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>7, 'name'=>'Viagens', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        AuxNetwork::insert([
            ['id'=>1, 'name'=>'MGID', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Google GAM', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Google ADS', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        Website::insert([
            ['id'=>1, 'company_id'=>1, 'name'=>'Alerta Jornal', 'url'=>'alertajornal.com.br', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'company_id'=>1, 'name'=>'Bona News', 'url'=>'bonanews.com.br', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'company_id'=>1, 'name'=>'Invest Agora', 'url'=>'investagora.com.br', 'category_id'=>2, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>4, 'company_id'=>1, 'name'=>'Papo Invest', 'url'=>'papoinvest.com.br', 'category_id'=>2, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>5, 'company_id'=>1, 'name'=>'Techzando', 'url'=>'techzando.com.br', 'category_id'=>3, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
        ]);

        Source::insert([
            ['id'=>1, 'name'=>'CNN', 'url'=>'https://cnnbrasil.com.br', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Jovem Pan', 'url'=>'https://jovempan.com.br', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Poder 360', 'url'=>'https://www.poder360.com.br', 'category_id'=>1, 'status_id'=>1, 'created_at'=>$date, 'updated_at'=>$date],
        ]);

    }
}