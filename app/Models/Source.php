<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    public $table = 'sources';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'doc'        => 'object',
        'template'   => 'object',
    ];

    CONST STATUS_ACTIVE   = 1;
    CONST STATUS_PENDING  = 0;
    CONST STATUS_INACTIVE = -1;
    CONST STATUS_INVALID  = -2;
    CONST STATUS = [1=>"Ativo", 0=>"Pendente", -1=>"Inativo", -2=>"Invalido"];

    CONST TYPE_WP = 1;
    CONST TYPE_CUSTOM = 2;
    CONST TYPE_CUSTOM_LIST = 3;
    CONST TYPES = [1=>"WP", 2=>"Custom", 3=>"Cutsom LIST"];

    ####################
    ### RELATIONSHIP ###
    
    public function Company() {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function Category() {
        return $this->hasOne(AuxCategory::class, 'id', 'category_id');
    }

    public function Posts() {
        return $this->hasMany(SourcePost::class, 'source_id', 'id');
    }

    ###############
    ### METHODS ###

    public function getStatus() {
        return self::STATUS[$this->status_id];
    }

    public function getType() {
        return self::TYPES[$this->type_id];
    }

}
