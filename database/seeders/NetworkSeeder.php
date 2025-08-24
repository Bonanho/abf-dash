<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DateTime;

use App\Models\Network;

class NetworkSeeder extends Seeder
{   
    public function run()
    {   
        $date = (new DateTime())->format("Y-m-d H:i:s");
        
        Network::insert([
            ['id'=>1, 'name'=>'MGID', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>2, 'name'=>'Google GAM', 'created_at'=>$date, 'updated_at'=>$date],
            ['id'=>3, 'name'=>'Google ADS', 'created_at'=>$date, 'updated_at'=>$date],
        ]);
    }
}