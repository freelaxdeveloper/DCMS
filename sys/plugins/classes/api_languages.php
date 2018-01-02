<?php
namespace App;

use App\{languages,api_controller};

class api_languages implements api_controller
{
    public static function get($request_data)
    {
        return languages::getList();
    }
} 