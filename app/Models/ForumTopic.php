<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use Dcms\Models\{ForumCategory,ForumTheme};
use App\App\App;

class ForumTopic extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function category()
    {
        return $this->hasOne(ForumCategory::class, 'id', 'id_category');
    }

    public function themes()
    {
        return $this->hasMany(ForumTheme::class, 'id_topic', 'id');
    }

    public function scopeGroup($query)
    {
        return $query->where([
            ['theme_view', '1'],
            ['group_show', '<=', App::user()->group],
        ])->whereHas('category', function ($query) {
            $query->group();
        });
    }
}