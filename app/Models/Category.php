<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = 'categories';

    protected $casts = [
        'doc' => 'object',
    ];

    CONST STATUS_ACTIVE = 1;
    CONST STATUS_PENDING = 0;
    CONST STATUS_INACTIVE = -1;
    CONST STATUS = [1=>"Ativo", 0=>"Pendente", -1=>"Inativo"];

    ####################
    ### RELATIONSHIP ###

    ###############
    ### METHODS ###
    
    public function getStatus() {
        return self::STATUS[$this->status_id];
    }
}
