<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
class Categories extends Controller
{
        public function index()
    {
        $categories = Category::all();

        return view('category.index', compact('categories'));
    }

    public function edit( $categoryId = null )
    {
        $category = Category::find( codeDecrypt($categoryId) );
        $categories = Category::all()->pluck('name', 'id');

        return view('category.edit', compact('category', 'categories'));
    }

    public function store( Request $request )
    {
        try
        {
            if( $request->id ){
                $category = Category::find( codeDecrypt($request->id) );
            } else {
                $category = new Category();
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