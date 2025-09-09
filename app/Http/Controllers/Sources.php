<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Source;
use App\Models\AuxCategory;
use App\Models\SourcePost;

class Sources extends Controller
{
    public function index()
    {
        $sources = Source::all();

        return view('source.index', compact('sources'));
    }

    public function edit( $sourceId = null )
    {
        $source = Source::find( codeDecrypt($sourceId) );
        $categories = AuxCategory::all()->pluck('name', 'id');

        return view('source.edit', compact('source', 'categories'));
    }

    public function store( Request $request )
    {
        try
        {
            if( $request->id ){
                $source = Source::find( codeDecrypt($request->id) );
            } else {
                $source = new Source();
            }

            $source->category_id = $request->category_id;
            $source->name        = $request->name;
            $source->url         = $request->url;
            $source->status_id   = $request->status_id;

            $source->save();

            return redirect()->route('source'); 
        }
        catch (\Exception $err) 
        {
            sessionMessage("error", "Erro ao salvar campanha:<br> {$err->getMessage()}");

            return redirect()->back();
        }

    }

    ################
    ## Fila de posts

    public function sourcePostList()
    {
        $posts = SourcePost::all();

        return view('source.source-posts', compact('posts'));
    }

    public function sourcePostStore( Request $request )
    {
        $posts = SourcePost::all();

        return view('source.source-posts', compact('posts'));
    }

}
