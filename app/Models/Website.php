<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    public $table = 'websites';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'config' => 'object',
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
        return $this->hasOne(AuxCategory::class, 'id', 'category_id');
    }

    public function Sources() {
        return $this->hasMany(WebsiteSource::class, 'website_id', 'id');
    }

    ###############
    ### METHODS ###

    public function getStatus() {
        return self::STATUS[$this->status_id];
    }

    public function getNetworks() 
    {
        $networks = $this->networks;
        //dd($this->networks);
    }
}
