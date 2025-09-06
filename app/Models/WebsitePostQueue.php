<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsitePostQueue extends Model
{
    public $table = 'websites_posts_queue';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'doc' => 'object',
    ];

    CONST STATUS_DONE = 1;
    CONST STATUS_PROCESSING = 11;
    CONST STATUS_PENDING = 0;
    CONST STATUS_ERROR = -1;
    CONST STATUS = [1=>"Concluido", 0=>"Pendente", -1=>"Erro", 11=>"Processando"];

    ####################
    ### RELATIONSHIP ###

    public function Website() {
        return $this->belongsTo(Website::class, 'website_id', 'id');
    }

    public function Source() {
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }

    public function SourcePost() {
        return $this->belongsTo(SourcePost::class, 'source_post_id', 'id');
    }

    ###############
    ### METHODS ###
    
    public function getStatus() {
        return self::STATUS[$this->status_id];
    }

    public function setStatus( $statusId ) {
        $this->status_id = $statusId;
        $this->save();
        return true;
    }
}
