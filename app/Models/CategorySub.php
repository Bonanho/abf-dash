<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySub extends Model
{
    public $table = 'categories_sub';

    protected $casts = [
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###

    ###############
    ### METHODS ###
}
