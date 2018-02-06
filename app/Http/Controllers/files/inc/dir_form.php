<?php
defined('DCMS') or die();
use App\{misc,form};
use App\App\App;

// файл отвечает за отображение возможных действий
if ($access_write) {
    // выгрузка и импорт файлов
    switch (@$_GET ['act']) {
        case 'file_upload' : {
            $max_file_uploads = ini_get('max_file_uploads');
            $upload_max_filesize = misc::returnBytes(ini_get('upload_max_filesize'));
            $post_max_size = misc::returnBytes(ini_get('post_max_size'));
            $memory_limit = misc::returnBytes(ini_get('memory_limit'));

            if ($memory_limit > 0) {
                $limit_size = min($upload_max_filesize, $post_max_size, $memory_limit);
            } else { // локально может отсутствовать лимит по памяти
                $limit_size = min($upload_max_filesize, $post_max_size);
            }

            $form = new form('?' . passgen());
            $form->fileMultiple('files[]', __('Файлы (мультивыбор)'));
            $form->bbcode(__('Максимальный размер всех файлов не должен превышать %s', misc::getDataCapacity($limit_size)));
            $form->bbcode(__('Максимальное кол-во файлов: %s', $max_file_uploads));
            $form->bbcode(__('[b]Данные ограничения настраиваются администратором сервера[b]'));
            $form->button(__('Выгрузить'));
            $form->display();
        }
            break;
    }

    $doc->act(__('Выгрузить файлы'), '?act=file_upload');
}

if ($access_edit) {
    // изменеение параметров
    switch (@$_GET ['act']) {
        case 'file_import' : {
            $form = new form('?' . passgen());
            $form->text('url', __('URL'), 'http://');
            $form->button(__('Импортировать'), 'file_import');
            $form->display();
        }
            break;
        case 'write_dir' : {
            $form = new form('?' . passgen());
            $form->text('name', __('Название папки'));
            $form->bbcode('* ' . __('На сервере создастся папка на транслите'));
            $form->button(__('Создать'), 'write_dir');
            $form->display();
        }
            break;

        case 'edit_unlink' : {
            if ($rel_path) {
                $form = new form('?' . passgen());
                $form->captcha();
                $form->bbcode('* ' . __('Все данные, находящиеся в этой папке будут безвозвратно удалены'));
                $form->button(__('Удалить'), 'edit_unlink');
                $form->display();
            }
        }
            break;
        case 'edit_path' : {
            // перемещение папки
            $options = array();
            // список папок в загруз-центре
            $root_dir = new files(FILES . '/.downloads');
            $dirs = $root_dir->getPathesRecurse($dir);
            foreach ($dirs as $dir2) {

                if ($dir2->group_show > App::user()->group || $dir2->group_write > App::user()->group) {
                    // если нет прав на чтение папки или на запись в папку, то пропускаем
                    continue;
                }

                if ($dir2->path_rel == $dir->path_rel) {
                    $options [] = array($dir2->path_rel, $dir2->getPathRu(), true);
                } else {
                    $options [] = array($dir2->getPath(), text::toValue($dir2->getPathRu() . ' <- ' . $dir->runame));
                }
            }

            // список папок обменника
            $root_dir = new files(FILES . '/.obmen');
            $dirs = $root_dir->getPathesRecurse($dir);
            foreach ($dirs as $dir2) {

                if ($dir2->group_show > App::user()->group || $dir2->group_write > App::user()->group) {
                    // если нет прав на чтение папки или на запись в папку, то пропускаем
                    continue;
                }

                if ($dir2->path_rel == $dir->path_rel) {
                    $options [] = array($dir2->path_rel, $dir2->getPathRu(), true);
                } else {
                    $options [] = array($dir2->getPath(), text::toValue($dir2->getPathRu() . ' <- ' . $dir->runame));
                }
            }

            $form = new form('?' . passgen());
            $form->select('path_rel_new', __('Новый путь'), $options);
            $form->button(__('Применить'), 'edit_path');
            $form->display();

        }
            break;
        case 'edit_prop' : {
            $groups = groups::load_ini(); // загружаем массив групп

            $form = new form('?' . passgen());
            $form->text('name', __('Название папки') . ' *', $dir->runame);
            $form->textarea('description', __('Описание'), $dir->description);

            if ($rel_path)
                $form->text('position', __('Позиция') . ' **', $dir->position);

            $order_keys = $dir->getKeys();
            $options = array();
            foreach ($order_keys as $key => $key_name) {
                $options [] = array($key, $key_name, $key == $dir->sort_default);
            }
            $form->select('sort_default', __('Сортировка по-умолчанию'), $options);

            $options = array();
            foreach ($groups as $type => $value) {
                $options [] = array($type, $value ['name'], $type == $dir->group_show);
            }
            $form->select('group_show', __('Просмотр папки') . ' ***', $options);

            $options = array();
            foreach ($groups as $type => $value) {
                $options [] = array($type, $value ['name'], $type == $dir->group_write);
            }
            $form->select('group_write', __('Выгрузка файлов'), $options);

            $options = array();
            foreach ($groups as $type => $value) {
                $options [] = array($type, $value ['name'], $type == $dir->group_edit);
            }
            $form->select('group_edit', __('Изменение параметров и создание папок'), $options);

            if ($rel_path && $dir->name{0} !== '.')
                $form->bbcode('* ' . __('На сервере папка будет на транслите'));
            else
                $form->bbcode('* ' . __('Изменится только отображаемое название'));
            if ($rel_path)
                $form->bbcode('** ' . __('Если у папок одинаковая позиция, то они сортируются по имени'));
            $form->bbcode('*** ' . __('При большом кол-ве вложенных объектов изменение данного параметра может затянуться (и подвесить сервер)'));

            $form->textarea('meta_description', __('Описание') . ' [META]', $dir->meta_description);
            $form->textarea('meta_keywords', __('Ключевые слова (через запятую)') . ' [META]', $dir->meta_keywords);

            $form->button(__('Применить'), 'edit_prop');
            $form->display();
        }
            break;
    }

    $doc->act(__('Импортировать файл'), '?act=file_import');
    $doc->act(__('Создать папку'), '?order=' . $order . '&amp;act=write_dir');
    $doc->act(__('Параметры'), '?order=' . $order . '&amp;act=edit_prop');

    if ($rel_path && $dir->name{0} !== '.') {
        $doc->act(__('Перемещение'), '?order=' . $order . '&amp;act=edit_path');
        $doc->act(__('Удаление папки'), '?order=' . $order . '&amp;act=edit_unlink');
    }
}