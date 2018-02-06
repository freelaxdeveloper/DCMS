<?php
defined('DCMS') or die;
use App\{files,files_file,download};
use App\App\App;

$pathinfo = pathinfo($abs_path);
$dir = new files($pathinfo['dirname']);

if ($dir->group_show > App::user()->group)$doc->access_denied(__('У Вас нет прав для просмотра файлов в данной папке'));

$file = new files_file($pathinfo['dirname'], $pathinfo['basename']);

if ($file->group_show > App::user()->group)$doc->access_denied(__('У Вас нет прав для просмотра данного файла'));

$doc->clean();
$f = new download($abs_path, $abs_path);
$downloaded = $f->output();

$file->downloads += round($file->size / $downloaded, 7);
exit;