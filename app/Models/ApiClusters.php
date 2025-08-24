<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClusters extends Model
{
    public $table = 'api_clusters';

    protected $casts = [
        'category' => 'object',
        'list' => 'object',
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###
    public function List() {
        return $this->hasMany(ClusterList::class, 'cluster_id', 'id');
    }

    ###############
    ### METHODS ###
}
