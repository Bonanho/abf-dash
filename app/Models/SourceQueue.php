<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceQueue extends Model
{
    public $table = 'sources_queue';

    protected $casts = [
        'doc'       => 'object',
        'post_data' => 'object',
    ];

    CONST STATUS_DONE       = 2;
    CONST STATUS_PROCESSING = 1;
    CONST STATUS_PENDING    = 0;
    CONST STATUS_ERROR      = -1;
    CONST STATUS = [2=>"Concluido", 1=>"Proccessando", 0=>"Pendente", -1=>"Erro"];

    ####################
    ### RELATIONSHIP ###
    
    public function Source() {
        return $this->belongsTo(Source::class, 'source_id', 'id');
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

    public static function register( $source, $postData )
    {
        $queueExists = SourceQueue::where("source_id", $source->id)->where("post_id",$postData->id)->count();

        if( $queueExists ){
            return null;
        }

        $sourceQueue = new SourceQueue();
        
        $sourceQueue->source_id = $source->id;
        $sourceQueue->post_id   = $postData->id;
        $sourceQueue->endpoint  = $postData->endpoint;
        $sourceQueue->doc       = $postData->data;

        $sourceQueue->save();

        return $sourceQueue;
    }

}
