<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Chat_mini extends Model{
    protected $table = 'chat_mini';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'id_user');
    }
}