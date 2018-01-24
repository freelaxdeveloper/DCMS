<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Browser,User};

class UserOnline extends Model{
    protected $table = 'users_online';
    public $timestamps = false;
    protected $fillable = ['time_login','time_last','request','id_browser','id_user','conversions','ip_long'];

    public function browser()
    {
        return $this->hasOne(Browser::class, 'id', 'id_browser');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
}