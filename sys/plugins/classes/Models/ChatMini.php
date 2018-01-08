<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ChatMini extends Model{
    protected $table = 'chat_mini';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
}