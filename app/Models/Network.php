<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    public $table = 'networks';

    protected $casts = [
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###

    ###############
    ### METHODS ###
}
