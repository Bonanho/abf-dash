<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportPost extends Model
{
    public $table = 'reports_post';

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
