<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportAds extends Model
{
    public $table = 'reports_ads';

    protected $casts = [
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###
    public function Website() {
        return $this->belongsTo(Website::class, 'website_id', 'id');
    }

    ###############
    ### METHODS ###
}
