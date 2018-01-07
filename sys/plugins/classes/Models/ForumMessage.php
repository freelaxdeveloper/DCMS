<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ForumMessage extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function topic()
    {
        return $this->hasOne('App\Models\ForumTopic', 'id', 'id_topic');
    }
    public function category()
    {
        return $this->hasOne('App\Models\ForumCategory', 'id', 'id_category');
    }
    public function theme()
    {
        return $this->hasOne('App\Models\ForumTheme', 'id', 'id_theme');
    }

    public function scopeGroup($query, $user)
    {
        return $query->where('group_show', '<=', $user->group)
            ->whereHas('theme', function ($query) use ($user) {
                $query->group($user);
            });
    }
    
}