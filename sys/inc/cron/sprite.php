<?php
use App\{cache,cache_events,misc,sprite};

if (!cache_events::get('check_sprite')) {
    cache_events::set('check_sprite', true, mt_rand(60, 180));
    $icons_paths = (array) @glob(H.'/public/images/icons/*.png');
    $sprite_icons_list_cache = cache::get('sprite_icons');
    $need_update_sprite = false;

    $sprite_icons_list = array();
    foreach ($icons_paths as $path) {
        $icon_name = basename($path);
        $sprite_icons_list[$icon_name] = filesize($path).'.'.filemtime($path);
    }

    if (!is_array($sprite_icons_list_cache)) {
        $need_update_sprite = true;
        misc::log('Нет данных о текущем спрайте', 'cron');
    } else {
        foreach ($sprite_icons_list as $key => $value) {
            if (!array_key_exists($key, $sprite_icons_list_cache) || $sprite_icons_list_cache[$key] !== $value) {
                $need_update_sprite = true;
                misc::log('Изображение '.$icon_name.'необходимо обновить', 'cron');
            }
        }
    }

    cache::set('sprite_icons', $sprite_icons_list, 86400);

    if ($need_update_sprite) {
        misc::log('Собираем новый спрайт', 'cron');
        $sprite = new sprite();
        $sprite->addImages($icons_paths);
        $sprite->bindIndexes();
        $sprite->saveSpriteImage(H.'/public/images/icons.png');
        $sprite->saveSpriteCss(H.'/public/css/.common/icons.css', '/images/icons.png', SPRITE_CLASS_PREFIX);
        misc::log('Новый спрайт собран', 'cron');
    }
}
