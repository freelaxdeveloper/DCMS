<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use Dcms\Models\{ForumTheme,User};

class ForumHistory extends Model{
    public $timestamps = false;
    protected $fillable = ['id_message','id_user','time','message'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
}