<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\CategorySub;
use App\Models\Cluster;
use App\Models\ClusterList;

class Clusters extends Controller
{
    public function index()
    {
        $categories    = Category::all();
        $categoriesSub = CategorySub::all();
        $clusters      = Cluster::all();
        $clustersList  = ClusterList::all();

        // dd("teste", $categories->toArray(), $categoriesSub->toArray(), $clusters->toArray(), $clustersList->toArray());

        return view('cluster.index', compact('clusters'));
    }

    // public function index2()
    // {   
    //     if(hasProfile("Retail") || hasProfile("Admin")){
    //         $retailId = array_keys(getRetails())[0];
    //         return redirect()->route('video', [VideoService::TYPE_CONTENT, codeEncrypt(@$retailId)]);
    //     }

    //     $retails = getRetails("obj");

    //     if($retails->count() == 1 && !hasScope('Super'))
    //         return redirect()->route('retail-edit', codeEncrypt($retails->first()->id));

    //     return view('retail.index', compact('retails'));
    // }

    // public function edit( $id = null )
    // {   
    //     $retail = null;
    //     if( $id ) {
    //         $retail = Retail::find( codeDecrypt($id) );
    //     }

    //     $sectors = Retail::SECTOR_TYPE_LIST;
    //     $retailGroups = RetailGroup::where('status_id', RetailGroup::STATUS_ACTIVE)->get();

    //     return view('retail.edit', compact('retail', 'sectors', 'retailGroups'));
    // }

    // public function store( Request $request )
    // { 
    //     try{
    //         if( $request->id ) {
    //             $retail = Retail::find( codeDecrypt($request->id) );
    //         }
    //         else {
    //             $retail = new Retail();
    //         }
            
    //         $retail->storeOrUpdate($request);

    //         sessionMessage("success", "Varejo criado/atualizado com sucesso.");
            
    //         return redirect()->route('retail-edit',codeEncrypt($retail->id) );
    //     }catch (\Exception $err)
    //     {
    //         sessionMessage("error", "Erro ao buscar varejo: {$err->getMessage()}");

    //         return redirect()->back();
    //     }
    // }


    // // API
    // public function fetchOptions(Request $request)
    // {
    //     $retail = Retail::find($request->retailId);
    //     if(!$retail)
    //     {
    //         return (object) [
    //             'branches' => [],
    //             'screens' => [],
    //             'categories' => [],
    //          ];
    //     }
    //     $branches = RetailBranch::select('id', 'name')->where('retail_id', $retail->id)->where('status_id', RetailBranch::STATUS_ACTIVE)->get();
    //     $categories = Category::select('id', 'name')->where('sector_type', $retail->sector_type)->where('status_id', RetailBranch::STATUS_ACTIVE)->get();

    //     $screens = Screen::select('id', 'name')->where('status_id', Screen::STATUS_ACTIVE)->where('retail_id', $retail->id);
    
    //     if(isset($request->orientationType))
    //         $screens = $screens->where('orientation_type', $request->orientationType);
    //     if(isset($request->branchIds))
    //         $screens = $screens->whereIn('branch_id', $request->branchIds);
    //     if(isset($request->categoryId))
    //         $screens = $screens->where('category_id', $request->categoryId);

    //     $screens = $screens->get();

    //     return (object) [
    //         'branches' => $branches,
    //         'screens' => $screens,
    //         'categories' => $categories,
    //      ];
    // }


    // public function getDocInfo ($doc)
    // {
    //     if(getCnpjInfoApi($doc))
    //         return json_encode(true);
    //     else
    //         return json_encode(false);
    // }


}
