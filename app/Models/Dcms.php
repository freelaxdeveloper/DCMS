<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;

class Dcms extends Model{
    protected $fillable = ['key'];
    protected $table = 'dcms_settings';

    protected $casts = [
        'options' => 'array',
    ];
}