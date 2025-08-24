<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $table = 'companies';

    protected $casts = [
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###

    ###############
    ### METHODS ###
}
