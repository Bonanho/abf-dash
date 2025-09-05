<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $table = 'companies';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###

    ###############
    ### METHODS ###
}
