<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{ForumTopic,ForumCategory,ForumTheme,ForumRating,User};

class ForumMessage extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

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

    public function scopeGroup($query, $user)
    {
        return $query->where('group_show', '<=', $user->group)
            ->whereHas('theme', function ($query) use ($user) {
                $query->group($user);
            });
    }
    
}