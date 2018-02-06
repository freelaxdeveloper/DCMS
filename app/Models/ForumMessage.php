<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use Dcms\Models\{ForumTopic,ForumCategory,ForumTheme,ForumRating,User};
use App\App\App;

class ForumMessage extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];
    // привязываем тему, теперь при (изменении/добавлении) сообщения update_at обновится и в теме
    //protected $touches = ['theme']; 

    public function topic()
    {
        return $this->hasOne(ForumTopic::class, 'id', 'id_topic');
    }
    public function category()
    {
        return $this->hasOne(ForumCategory::class, 'id', 'id_category');
    }
    public function theme()
    {
        return $this->hasOne(ForumTheme::class, 'id', 'id_theme');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }

    public function ratings()
    {
        return $this->hasMany(ForumRating::class, 'id_message');
    }

    public function scopeGroup($query)
    {
        return $query->where('group_show', '<=', App::user()->group)
            ->whereHas('theme', function ($query) {
                $query->group();
            });
    }
    
}