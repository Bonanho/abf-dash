<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AuxCategory;
class Categories extends Controller
{
        public function index()
    {
        $categories = AuxCategory::all();

        return view('category.index', compact('categories'));
    }

    public function edit( $categoryId = null )
    {
        $category = AuxCategory::find( codeDecrypt($categoryId) );

        return view('category.edit', compact('category'));
    }

    public function store( Request $request )
    {
        try
        {
            if( $request->id ){
                $category = AuxCategory::find( codeDecrypt($request->id) );
            } else {
                $category = new AuxCategory();
            }

            $category->name        = $request->name;
            $category->status_id   = $request->status_id;

            $category->save();

            return redirect()->route('category'); 
        }
        catch (\Exception $err) 
        {
            sessionMessage("error", "Erro ao salvar campanha:<br> {$err->getMessage()}");

            return redirect()->back();
        }

    }

}