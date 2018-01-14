<?php
include_once '../sys/inc/start.php';
use App\{document,current_user,sprite,user,listing,misc,text,pages,groups};
use App\Models\{ForumView,ForumTheme,ForumMessage};
use App\App\App;

$doc = new document();
$doc->title = __('Форум');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора темы'));
    exit;
}
$id_theme = (int)$_GET['id'];

if (!$theme = ForumTheme::group(App::user())->find($id_theme)) {
    header('Refresh: 1; url=./');
    $doc->err(__('Тема не доступна'));
    exit;
}

if (App::user()->group) {
    $theme->views()->updateOrCreate([
        'id_user' => App::user()->id
    ])->increment('views', 1);
}

$doc->title .= ' - ' . $theme->name;

$doc->keywords[] = __('Форум');
$doc->keywords[] = $theme->name;
$doc->keywords[] = $theme->topic->name;
$doc->keywords[] = $theme->category->name;

$pages = new pages();
$pages->posts = $theme->messages()->group()->count();
$doc->description = __('Форум') . ' - ' . $theme->name . ' - ' . __('Страница %s из %s', $pages->this_page, $pages->pages);

include 'inc/theme.votes.php';

$img_thumb_down = '<a href="{url}" class="DCMS_thumb_down ' . implode(' ', sprite::getClassName('thumb_down', SPRITE_CLASS_PREFIX)) . '"></a>';
$img_thumb_up = '<a href="{url}" href="" class="DCMS_thumb_up ' . implode(' ', sprite::getClassName('thumb_up', SPRITE_CLASS_PREFIX)) . '"></a>';

$messages = $theme->messages()
    ->group()->get()
    ->forPage($pages->this_page, App::user()->items_per_page);

$listing = new listing();
foreach ($messages AS $message) {
    $my_rating = $message->ratings{0}->rating ?? 0;

    $post = $listing->post();
    $post->id = 'message' . $message->id;

    if ($user->group) {
        $post->action('quote', "message.php?id_message={$message->id}&amp;quote"); // цитирование
    }

    if ($user->group > $message->user->group || ($user->id && $user->id == $theme->id_moderator) || $user->group == groups::max()) {
        if ($theme->group_show <= 1) {
            if ($message->group_show <= 1) {
                $post->action('hide', "message.edit.php?id={$message->id}&amp;return=" . URL . "&amp;act=hide&amp;" . passgen()); // скрытие
            } else {
                $post->action('show', "message.edit.php?id={$message->id}&amp;return=" . URL . "&amp;act=show&amp;" . passgen()); // показ

                $post->bottom = __('Сообщение скрыто');
            }
        }
        $post->action('edit', "message.edit.php?id={$message->id}&amp;return=" . URL); // редактирование
    } elseif ($user->id == $message->id_user && TIME < $message->time + 600) {
        // автору сообщения разрешается его редактировать в течении 10 минут
        $post->action('edit', "message.edit.php?id={$message->id}&amp;return=" . URL); // редактирование
    }

    if ($message->user->group <= $user->group && $user->id != $message->user->id) {
        if ($user->group >= 2)
            // бан
            $post->action('complaint', "/dpanel/user.ban.php?id_ank={$message->id_user}&amp;return=" . URL . "&amp;link=" . urlencode("/forum/message.php?id_message={$message->id}"));
        else
            // жалоба на сообщение
            $post->action('complaint', "/complaint.php?id={$message->id_user}&amp;return=" . URL . "&amp;link=" . urlencode("/forum/message.php?id_message={$message->id}"));
    }

    $post->title = $message->user->login;
    $post->icon($message->user->icon);

    $doc->last_modified = $message->time;
    $post->time = misc::when($message->time);
    $post->url = 'message.php?id_message=' . $message->id;
    $post->content = text::for_opis($message->message);

    if ($message->edit_id_user && ($message->user->group < $user->group || $message->user->id == $user->id)) {
        $ank_edit = new user($message->edit_id_user);
        $post->bottom .= ' <a href="message.history.php?id=' . $message->id . '&amp;return=' . URL . '">' . __('Изменено') . '(' . $message->edit_count . ')</a> ' . $ank_edit->login . ' (' . misc::when($message->edit_time) . ')<br />';
    }

    if ($user->group && $user->id != $message->user->id) {
        if ($my_rating === 0 && $user->balls - $dcms->forum_rating_down_balls >= 0) {
            $post->bottom .= str_replace('{url}', 'message.rating.php?id=' . $message->id . '&amp;change=down&amp;return=' . URL . urlencode('#' . $post->id), $img_thumb_down);
        }

        if ($my_rating === 0) {
            $post->bottom .= ' ' . __('Рейтинг: %s / %s', '<span class="DCMS_rating_down">' . $message['rating_down'] . '</span>', '<span class="DCMS_rating_up">' . $message->rating_up . '</span>') . ' ';
        } else {
            $post->bottom .= ' ' . __('Рейтинг: %s / %s / %s', '<span class="DCMS_rating_down">' . $message->rating_down . '</span>', $my_rating, '<span class="DCMS_rating_up">' . $message->rating_up . '</span>') . ' ';
        }

        if ($my_rating === 0)
            $post->bottom .= str_replace('{url}', 'message.rating.php?id=' . $message->id . '&amp;change=up&amp;return=' . URL . urlencode('#' . $post->id), $img_thumb_up);
    } else {
        $post->bottom .= ' ' . __('Рейтинг: %s / %s', '<span class="DCMS_rating_down">' . $message->rating_down . '</span>', '<span class="DCMS_rating_up">' . $message->rating_up . '</span>') . ' ';
    }

    $post_dir_path = H . '/sys/files/.forum/' . $theme->id . '/' . $message->id;
    if (@is_dir($post_dir_path)) {
        $listing_files = new listing();
        $dir = new files($post_dir_path);
        $content = $dir->getList('time_add:asc');
        $files = &$content['files'];
        $count = count($files);
        for ($i = 0; $i < $count; $i++) {
            $file = $listing_files->post();
            $file->title = text::toValue($files[$i]->runame);
            $file->url = "/files" . $files[$i]->getPath() . ".htm?order=time_add:asc";
            $file->content[] = $files[$i]->properties;
            $file->icon($files[$i]->icon());
            $file->image = $files[$i]->image();
        }
        if ($count){
            $post->content .= $listing_files->fetch();
        }
    }
}

$listing->display(__('Сообщения отсутствуют'));
$pages->display('theme.php?id=' . $theme->id . '&amp;'); // вывод страниц

if ($theme->group_write <= $user->group) {
    $doc->act(__('Написать сообщение'), 'message.new.php?id_theme=' . $theme->id . "&amp;return=" . URL);
}
if ($user->group >= 2 || $theme->group_edit <= $user->group || ($user->id && $user->id == $theme->id_moderator)) {
    $doc->act(__('Действия'), 'theme.actions.php?id=' . $theme->id);
}

$doc->ret($theme->topic->name, 'topic.php?id=' . $theme->topic->id);
$doc->ret($theme->category->name, 'category.php?id=' . $theme->category->id);
$doc->ret(__('Форум'), './');