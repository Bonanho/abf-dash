<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinIncome extends Model
{
    public $table = 'fin_incomes';

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

    ###############
    ### METHODS ###
}
