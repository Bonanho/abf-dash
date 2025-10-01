<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Website;
use App\Models\Company;
use App\Models\AuxCategory;
use App\Models\WebsitePost;
use App\Models\WebsitePostQueue;
use App\Models\WebsiteSource;

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

            $config = (@$website->config) ?? (object) [];
            $config->wpUser  = $request->wpuser;
            $config->wpPass  = $request->wppass;
            $config->siteMap = $request->sitemap;
            $website->config = $config;

            $useKeywords = ( $request->keywords!="" && strlen($request->keywords) > 3 ) ? true : false;
            $keywords = ($useKeywords) ? explode("|||",$request->keywords) : "";

            $doc = (@$website->doc) ?? (object) [];
            $doc->useKeywords = $useKeywords;
            $doc->keywords    = $keywords;
            $website->doc     = $doc;

            $website->save();

            return redirect()->route('website-edit',codeEncrypt($website->id)); 
        }
        catch (\Exception $err) 
        {
            sessionMessage("error", "Erro ao salvar campanha:<br> {$err->getMessage()}");

            return redirect()->back();
        }

    }

    ##################
    ## Website Sources
    
    public function wSourceIndex( $websiteId )
    {
        $websiteId = codeDecrypt($websiteId);
        $wSources = WebsiteSource::where("website_id",$websiteId)->get();

        return view('website.wsource-index', compact('websiteId','wSources'));
    }

    public function wSourceStore( Request $request )
    {
        try
        {
            $websiteId = codeDecrypt($request->website_id);
            $action = $request->action;

            if( $action=="config" ||  $action=="update" )
            {
                $wSourceId = codeDecrypt($request->id);
                $websiteSource = WebsiteSource::find($wSourceId);

                if( $action=="config" )
                {
                    $doc = (@$websiteSource->doc) ?? (object) [];
                    if( isset($request->rewrite) ){
                        $doc->rewrite = (int) $request->rewrite;
                    }
                    elseif( isset($request->defaultPostStatus) ){
                        $doc->defaultPostStatus = (int) $request->defaultPostStatus; 
                    }
                    $websiteSource->doc = $doc;
                }
                
            }
            elseif( $action == "create" )
            {
                $websiteSource = new WebsiteSource();
                
                $websiteSource->website_id  = $websiteId;
                $websiteSource->source_id  = $request->source_id;
            }

            $websiteSource->save();

            return redirect()->route('website-source',$request->website_id);
        }
        catch (\Exception $err) 
        {   //dd($err);
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
