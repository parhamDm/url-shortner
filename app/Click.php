<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * @property string user_clicker
 * @property string browser
 * @property boolean is_mobile
 * @property int url_id
 */
class Click extends Model
{
    protected $table = 'clicks';
    protected $primaryKey= 'id';
}
