<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClickCountBrowser extends Model
{
    protected $table = 'user_click_count_browser';
    protected $primaryKey= 'id';
    public $timestamps = false;
}
