<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;

class Browser extends Model{
    public $timestamps = false;
    protected $fillable = ['name','type'];

}