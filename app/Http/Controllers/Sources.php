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
        $types = Source::TYPES;

        return view('source.edit', compact('source', 'categories', 'types'));
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
            $source->type_id     = $request->type_id;

            $template = ($source->template) ?? (object) [];
            $template->listEndpoint = $request->tpt_list_endpoint;
            $template->wpEndpoint   = $request->tpt_wp_endpoint;
            $template->homeNew      = $request->tpt_home_new;
            $template->title        = $request->tpt_title;
            $template->content      = $request->tpt_content;
            $source->template = $template;

            $source->save();

            return redirect()->route('source-edit', codeEncrypt($source->id) ); 
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
