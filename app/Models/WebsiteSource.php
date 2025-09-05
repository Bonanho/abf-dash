<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSource extends Model
{
    public $table = 'websites_sources';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###
    
    public function Website() {
        return $this->belongsTo(Website::class, 'website_id', 'id');
    }

    public function Source() {
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }

    ###############
    ### METHODS ###
}
