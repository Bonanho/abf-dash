<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    public $table = 'clusters';

    protected $casts = [
        'category' => 'object',
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
