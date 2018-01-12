<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ForumMessage;

class ForumRating extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];
    public $table = 'forum_rating';

}