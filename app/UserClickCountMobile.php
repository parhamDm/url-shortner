<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClickCountMobile extends Model
{
    protected $table = 'user_click_count_mobile';
    protected $primaryKey= 'id';
    public $timestamps = false;
}
