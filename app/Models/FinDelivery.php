<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinDelivery extends Model
{
    public $table = 'fin_deliveries';

    protected $casts = [
        'doc' => 'object',
    ];

    ####################
    ### RELATIONSHIP ###
    
    public function Network() {
        return $this->belongsTo(AuxNetwork::class, 'network_id', 'id');
    }

    ###############
    ### METHODS ###
}
