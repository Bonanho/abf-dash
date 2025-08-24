<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCampaign extends Model
{
    public $table = 'reports_campaign';

    protected $casts = [
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###
    public function Campaign() {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }
    ###############
    ### METHODS ###
}
