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
use App\Models\WebsiteSource;

class InitialSeeder extends Seeder
{   
    public function run()
    {   
        $date = (new DateTime())->format("Y-m-d H:i:s");
        
        $password = Hash::make("super@123");
        User::insert([
            ['id'=>1, 'name'=>'SuperUser', 'profile_id'=>1, 'email'=>'super@user.com', 'password'=>$password, 'status_id'=>1, 'email_verified_at'=>$date, 'created_at'=>$date, 'updated_at'=>$date],
        ]);
    }
}