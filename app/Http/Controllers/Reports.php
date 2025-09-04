<?php

namespace App\Http\Controllers;

use App\Models\FinIncome;
use Illuminate\Http\Request;

use App\Models\WebsitePost;

class Reports extends Controller
{
    public function posts()
    {
        $reports = WebsitePost::all();

        return view('report.posts', compact('reports'));
    }

    public function ads()
    {
        $reports = FinIncome::all();

        return view('report.ads', compact('reports'));
    }

}