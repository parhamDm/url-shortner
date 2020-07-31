<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * @property string long_url
 * @property string short_url
 * @property int user_id
 */
class Url extends Model
{
    protected $table = 'url';
    protected $primaryKey= 'id';
    public $timestamps = false;
}
