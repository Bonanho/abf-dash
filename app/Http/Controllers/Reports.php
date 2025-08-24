<?php

namespace App\Http\Controllers;

use App\Models\ReportCampaign;
use Illuminate\Http\Request;

class Reports extends Controller
{
    public function campaign()
    {
        $reports = ReportCampaign::all();

        return view('report.index', compact('reports'));
    }

}