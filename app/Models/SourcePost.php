<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourcePost extends Model
{
    public $table = 'sources_posts';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'doc'       => 'object',
        'post_data' => 'object',
        'error' => 'object',
    ];

    CONST STATUS_DONE       = 1;
    CONST STATUS_PROCESSING = 11;
    CONST STATUS_PENDING    = 0;
    CONST STATUS_ERROR      = -1;
    CONST STATUS = [1=>"Concluido", 11=>"Proccessando", 0=>"Pendente", -1=>"Erro"];

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
        $exists = SourcePost::where("source_id", $source->id)->where("endpoint",$postData->endpoint)->count();
        if( $exists ){
            return null;
        }

        $sourcePost = new SourcePost();
        
        $sourcePost->source_id      = $source->id;
        $sourcePost->post_origin_id = $postData->id;
        $sourcePost->endpoint       = $postData->endpoint;
        $sourcePost->post_data      = $postData->data;

        $sourcePost->save();

        return $sourcePost;
    }

    public static function registerCustom( $source, $postData )
    {
        $exists = SourcePost::where("source_id", $source->id)->where("endpoint",$postData->url_original)->count();
        if( $exists ){
            return null;
        }

        $sourcePost = new SourcePost();
        
        $sourcePost->source_id      = $source->id;
        $sourcePost->post_origin_id = $postData->post_id;
        $sourcePost->endpoint       = $postData->url_original;
        $sourcePost->doc            = $postData;
        $sourcePost->status_id      = SourcePost::STATUS_DONE;

        $sourcePost->save();

        return $sourcePost;
    }

}
