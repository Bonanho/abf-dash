<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinDelivery extends Model
{
    public $table = 'fin_deliveries';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
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
