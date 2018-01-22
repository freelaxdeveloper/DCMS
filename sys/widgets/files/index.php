<?php
use App\{listing,text,files,misc};
use App\Models\User;

defined('DCMS') or die;
global $user;

$dir = new files(FILES . '/.downloads');
$content = $dir->getNewFiles();
$files = &$content['files'];
/** @var $files files_file[] */
$new_files = count($files);

$listing = new listing();

$post = $listing->post();
$post->highlight = true;
$post->icon('downloads');
$post->url = '/files/.downloads/';
$post->title = __('Загрузки');
if ($new_files)
    $post->counter = '+' . $new_files;

for ($i = 0; $i < $new_files && $i < $dcms->widget_items_count; $i++) {
    $ank = User::find($files[$i]->id_user);
    $post = $listing->post();
    $post->title = text::toValue($files[$i]->runame);
    $post->time = misc::when($files[$i]->time_add);
    $post->url = "/files" . $files[$i]->getPath() . ".htm";
    $post->image = $files[$i]->image();
    $post->icon($files[$i]->icon());
    if ($ank->id)
        $post->bottom = $ank->login;
    
}

if ($new_files > $dcms->widget_items_count) {
    $post = $listing->post();
    $post->icon('new');
    $post->url = '/files/new.php?dir=.downloads';
    $post->title = __('Все новые файлы');
}

$listing->display();