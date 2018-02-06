<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use Dcms\Models\ForumTheme;

class ForumView extends Model{
    // public $timestamps = false;
    protected $guarded = ['id'];

    public function themes()
    {
      return $this->belongsToMany(ForumTheme::class, 'forum_messages');
    }
}