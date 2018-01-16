<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\App\App;

class News extends Model{
    public $timestamps = false;
    protected $fillable = ['title','time','text','id_user'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
    public function getActionsAttribute(): array
    {
        $actions = [];
        if (4 <= App::user()->group) {
            if (!$this->sended) {
                $actions[] = ['url' =>  './news.send.php?id=' . $this->id, 'icon' => 'send'];
            }
            $actions[] = ['url' => './news.edit.php?id=' . $this->id, 'icon' => 'edit'];
            $actions[] = ['url' => './news.delete.php?id=' . $this->id, 'icon' => 'delete'];
        }
        return $actions;
    }
}