<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Website;
use App\Models\Company;
use App\Models\AuxCategory;
use App\Models\WebsitePost;
use App\Models\WebsitePostQueue;

class Websites extends Controller
{
    public function index()
    {
        $websites = Website::all();

        return view('website.index', compact('websites'));
    }

    public function edit( $websiteId = null )
    {
        $website = Website::find( codeDecrypt($websiteId) );
        $companies = Company::all()->pluck('name', 'id');
        $categories = AuxCategory::all()->pluck('name', 'id');

        return view('website.edit', compact('website', 'companies', 'categories'));
    }

    public function store( Request $request )
    {
        try
        {
            if( $request->id ){
                $website = Website::find( codeDecrypt($request->id) );
            } else {
                $website = new Website();
            }

            $website->company_id  = $request->company_id;
            $website->category_id = $request->category_id;
            $website->name        = $request->name;
            $website->url         = $request->url;
            $website->status_id   = $request->status_id;

            $website->save();

            return redirect()->route('website'); 
        }
        catch (\Exception $err) 
        {
            sessionMessage("error", "Erro ao salvar campanha:<br> {$err->getMessage()}");

            return redirect()->back();
        }

    }

    ################
    ## Posts Queue

    public function postsQueueList()
    {
        $queuePosts = WebsitePostQueue::all();

        return view('website.website-queue', compact('queuePosts'));
    }

    public function postsQueueStore( Request $request )
    {
        $posts = WebsitePostQueue::all();

        return redirect()->route("website-queue");
    }

    ################
    ## Posts

    public function postsList()
    {
        $posts = WebsitePost::all();

        return view('website.website-posts', compact('posts'));
    }

    public function postsStore( Request $request )
    {
        $posts = WebsitePost::all();

        return redirect()->route("website-post");
    }

}
