<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{ForumTheme,User};

class ForumHistory extends Model{
    public $timestamps = false;
    protected $fillable = ['id_message','id_user','time','message'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
}