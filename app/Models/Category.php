<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = 'categories';

    protected $casts = [
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###

    ###############
    ### METHODS ###
}
