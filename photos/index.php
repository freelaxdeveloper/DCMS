<?php 
include_once '../sys/inc/start.php';
use App\{document,files};

$doc = new document ();
$doc->title = __('Фотоальбомы');
$photos = new files ( FILES . '/.photos' );