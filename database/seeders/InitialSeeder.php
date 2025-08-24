<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DateTime;

use App\Models\User;
use App\Models\Company;

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

        Category::insert([
            ['id'=>1, 'name'=>'Notícias', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Tecnologia', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Finanças', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>4, 'name'=>'Entreterimento', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>5, 'name'=>'Saúde', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>6, 'name'=>'Viagens', 'created_at'=>$date, 'updated_at'=>$date],
        ]);

    }
}