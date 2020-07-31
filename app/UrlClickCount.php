<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UrlClickCount extends Model
{
    protected $table = 'url_click_count';
    protected $primaryKey= 'id';
    public $timestamps = false;
}
