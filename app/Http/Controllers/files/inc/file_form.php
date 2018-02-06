<?php
use App\{groups,form,url,files};
use App\App\App;

switch (@$_GET['act']) {
    case 'edit_prop':
    {
        $groups = groups::load_ini(); // загружаем массив групп

        $form = new form(new url);
        $form->text('name', __('Название файла') . ' *', $file->runame);
        $form->textarea('description', __('Описание'), $file->description);
        $form->textarea('description_small', __('Краткое описание'), $file->description_small);

        if ($file->group_edit <= App::user()->group) {
            $options = array();
            foreach ($groups as $type => $value) {
                $options[] = array($type, $value['name'], $type == $file->group_show);
            }
            $form->select('group_show', __('Просмотр/скачивание файла'), $options);

            $options = array();
            foreach ($groups as $type => $value) {
                $options[] = array($type, $value['name'], $type == $file->group_edit);
            }
            $form->select('group_edit', __('Изменение параметров'), $options);
        }

        $form->textarea('meta_description', __('Описание') . ' [META]', $file->meta_description);
        $form->textarea('meta_keywords', __('Ключевые слова (через запятую)') . ' [META]', $file->meta_keywords);

        $form->bbcode('* ' . __('На сервере имя файла будет на транслите'));
        $form->button(__('Применить'), 'edit_prop');
        $form->display();
    }
        break;
    case 'edit_path' :
    {
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
                $options [] = array($dir2->getPath(), text::toValue($dir2->getPathRu() . ' <- ' . $file->runame));
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
                $options [] = array($dir2->getPath(), text::toValue($dir2->getPathRu() . ' <- ' . $file->runame));
            }
        }

        $form = new form(new url());
        $form->select('path_rel_new', __('Новый путь'), $options);
        $form->button(__('Применить'), 'edit_path');
        $form->display();
    }
        break;
    case 'edit_unlink':
    {
        $form = new form(new url());
        if ($file->id_user && $file->id_user != App::user()->id)
            $form->textarea('reason', __('Причина удаления'));
        $form->bbcode(__('Подтвердите удаление файла'));
        $form->button(__('Удалить'), 'edit_unlink');
        $form->display();
    }
        break;
}


$doc->act(__('Скриншоты'), '?order=' . $order . '&amp;act=edit_screens');
$doc->act(__('Параметры файла'), '?order=' . $order . '&amp;act=edit_prop');
$doc->act(__('Переместить'), '?order=' . $order . '&amp;act=edit_path');
$doc->act(__('Удалить файл'), '?order=' . $order . '&amp;act=edit_unlink');