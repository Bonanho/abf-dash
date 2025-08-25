<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    public $table = 'sources';

    protected $casts = [
        'doc'    => 'object',
    ];

    CONST STATUS_ACTIVE = 1;
    CONST STATUS_PENDING = 0;
    CONST STATUS_INACTIVE = -1;
    CONST STATUS = [1=>"Ativo", 0=>"Pendente", -1=>"Inativo"];

    ####################
    ### RELATIONSHIP ###
    public function Company() {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function Category() {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    // public function Sources() {
    //     return $this->hasMany(WebsiteSource::class, 'cluster_id', 'id');
    // }

    ###############
    ### METHODS ###

    public function getStatus() {
        return self::STATUS[$this->status_id];
    }

    public function getNetworks() 
    {
        $networks = $this->networks;
        dd($this->networks);
    }
}
