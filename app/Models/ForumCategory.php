<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use App\App\App;

class ForumCategory extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function scopeGroup($query)
    {
        return $query->where('group_show', '<=', App::user()->group);
    }
}