<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ForumCategory extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function scopeGroup($query, $user)
    {
        return $query->where('group_show', '<=', $user->group);
    }
}