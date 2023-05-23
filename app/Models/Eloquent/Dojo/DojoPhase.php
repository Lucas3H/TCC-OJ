<?php

namespace App\Models\Eloquent\Dojo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DojoPhase extends Model
{
    use SoftDeletes;

    protected $fillable=[
        'name', 'description', 'passline', 'order'
    ];

    public function transformMarkdown(){
        $this->attributes['description'] = clean(convertMarkdownToHtml($this->attributes['description']));
    }

    public function dojos()
    {
        return $this->hasMany('App\Models\Eloquent\Dojo\Dojo', 'dojo_phase_id');
    }
}
