<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use Dcms\Models\ForumMessage;

class ForumRating extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];
    public $table = 'forum_rating';

}