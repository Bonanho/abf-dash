<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSource extends Model
{
    public $table = 'websites_sources';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'doc' => 'object',
    ];

    CONST STATUS_ACTIVE = 1;
    CONST STATUS_INACTIVE = 0;
    CONST STATUS = [1=>"Ativo", 0=>"Pausado"];

    ####################
    ### RELATIONSHIP ###
    
    public function Website() {
        return $this->belongsTo(Website::class, 'website_id', 'id');
    }

    public function Source() {
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }

    public function WPost() {
        return $this->hasMany(WebsitePost::class, 'website_source_id','id');
    }

    ###############
    ### METHODS ###

    public function getStatus() {
        return self::STATUS[$this->status_id];
    }

    public function getWpPostStatus()
    {
        // Principais status de posts no WordPress:
        $wpPostStatus["publish"]    = "Publicado";  // publicado imediatamente e visível no site.
        // $wpPostStatus["future"]     = "Agendado";  // agendado para publicação em uma data/hora futura (date ou date_gmt).
        $wpPostStatus["draft"]      = "Rascunho";  // rascunho (não visível ao público).
        $wpPostStatus["pending"]    = "Pendente";  // pendente de revisão (alguém com permissão precisa aprovar/publicar).
        // $wpPostStatus["private"]    = "Privado";  // publicado, mas apenas usuários logados com permissão conseguem ver.
        // $wpPostStatus["trash"]      = "Lixeira";  // enviado para a lixeira.
        // $wpPostStatus["auto"]       = "Auto";  // draft → rascunho automático criado pelo WordPress (geralmente quando você começa a escrever mas ainda não salvou nada).
        // $wpPostStatus["inherit"]    = "Anexos";  // usado para anexos (imagens, arquivos), herdam o status do post pai.

        return $wpPostStatus;
    }
}
