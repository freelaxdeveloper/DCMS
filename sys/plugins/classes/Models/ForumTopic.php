<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ForumTopic extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function category()
    {
        return $this->hasOne('App\Models\ForumCategory', 'id', 'id_category');
    }

    public function scopeGroup($query, $user)
    {
        return $query->where([
            ['theme_view', '1'],
            ['group_show', '<=', $user->group],
        ])->whereHas('category', function ($query) use ($user) {
            $query->group($user);
        });
    }
}