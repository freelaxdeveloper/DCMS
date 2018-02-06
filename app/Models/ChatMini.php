<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use Dcms\Models\User;

class ChatMini extends Model{
    protected $table = 'chat_mini';
    protected $fillable = ['id_user', 'message'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
}