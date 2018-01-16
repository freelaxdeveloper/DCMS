<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{News,User};
use App\App\App;

class NewsComment extends Model{
    public $timestamps = false;
    protected $fillable = ['id_news','time','id_user','text'];

    public function news()
    {
        return $this->hasOne(News::class, 'id', 'id_news');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
    public function getActionsAttribute(): array
    {
        $actions = [];
        if (2 <= App::user()->group) {
            $actions[] = ['url' => './news.delete.php?id=' . $this->id, 'icon' => 'delete'];
        }
        return $actions;
    }
}