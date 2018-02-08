<?php
namespace App;

use App\{native_templating,themes};
use App\App\App;

/**
 * Дизайн. Конфигуратор шаблонизатора.
 */
class design extends native_templating
{

    public $theme;

    function __construct()
    {
        parent::__construct();
        global $dcms, $user_language_pack, $user, $probe_theme;
        static $theme = false;
        if ($theme === false) {
            if (!empty($probe_theme) && themes::exists($probe_theme)) {
                $theme = themes::getThemeByName($probe_theme);
            } elseif (themes::exists(App::user()->theme)) {
                // пользовательская тема оформления
                $theme = themes::getThemeByName(App::user()->theme);
            } elseif (themes::exists($dcms->theme)) {
                // системная тема оформления
                $theme = themes::getThemeByName($dcms->theme);
            } elseif (($themes = themes::getThemesByType($dcms->browser_type))) {
                // тема оформления для типа браузера
                $theme = current($themes);
            } else {
                // любая тема оформления
                $theme = current(themes::getAllThemes());
                if (!$theme)
                    die('Не найдено ни одной совместимой темы оформления');
            }
        }

        $this->theme = $theme;

        // папка шаблонов
        $this->_dir_templates = H . '/resources/views/' . $theme->getName() . '/tpl/';

        // системные переменные
        $this->assign('theme', $theme);
        $this->assign('dcms', $dcms);
        $this->assign('copyright', $dcms->copyright, 2);
        $this->assign('lang', $user_language_pack);
        $this->assign('user', $user);
        $this->assign('path', '/resources/views/' . $theme->getName());
    }

    /**
     * Максимальная ширина изображения в зависимости от типа браузера и параметров темы
     */
    function img_max_width()
    {
        return $this->theme->getImgWidthMax();
    }

    /**
     * Ищет путь к указанной иконке.
     * @param string $name Имя иконки
     * @return string Путь к иконке
     */
    function getIconPath($name)
    {
        return '/images/icons/' . basename($name, '.png') . '.png';
    }
}