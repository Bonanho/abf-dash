<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use DateTime;

use App\Models\ApiClusters;

class ApiController extends Controller
{
    public function clustersManage(Request $request)
    {
        // POST - http://dash-crm.loc/api/clusters
        // {"CRMauth":"CRMBBA", "name":"teste", "description":"Descrição", "categories":["c1", "c2"], "list":[["cpf-111","email-111"],["cpf-222","email-222"]] }
        try 
        {
            if( $request->CRMauth == "CRMBBA" )
            {
                $name = $request->name;
                $description = $request->description;
                $categories = $request->categories;
                $list = $request->list;

                $apiClusters = new ApiClusters();
                $apiClusters->name = $name;
                $apiClusters->description = $description;
                $apiClusters->categories = json_encode($categories);
                $apiClusters->list = json_encode($list);
                $apiClusters->status_id = 0;
                $apiClusters->save();
                dd( $name, $description, $categories, $list );
            }
            else {
                return response()->json(['error' => 'Acesso NEGADO!'], 404);
            }
        }
        catch ( Exception $error )
        {   dd($error);
            return response()->json(['error' => 'Erro no processo!'], 500);
        }
        
        return true;
    }
}