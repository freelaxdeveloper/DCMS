<?php
namespace App;

use App\{design,widget,document_link,text,document_message,alignedxhtml,url,current_user};
use Jenssegers\Blade\Blade;
use App\App\App;

/**
 * Класс для формирования HTML документа.
 */
class document extends design
{
    public $title = 'Заголовок';
    public $description = '';
    public $keywords = [];
    public $last_modified = null;
    protected $err = [];
    protected $msg = [];
    protected $outputed = false;
    protected $actions = [];
    protected $returns = [];
    protected $tabs = [];
    protected $_echo_content = '';
    public $template = 'default';

    function __construct($group = 0)
    {
        parent::__construct();
        global $dcms;
        $this->title = $dcms->title;
        if ($group > App::user()->group) {
            $this->access_denied(__('Доступ к данной странице запрещен'));
        }
        ob_start();
    }

    /**
     * @param $name
     * @param string|url $url
     * @param bool $selected
     * @return document_link
     */
    function tab($name, $url, $selected = false)
    {
        return $this->tabs[] = new document_link(text::toValue($name), $url, $selected);
    }

    /**
     * @param $name
     * @param string|url $url
     * @return document_link
     */
    function ret($name, $url)
    {
        return $this->returns[] = new document_link(text::toValue($name), $url);
    }

    /**
     * @param $name
     * @param string|url $url
     * @return document_link
     */
    function act($name, $url)
    {
        return $this->actions[] = new document_link(text::toValue($name), $url);
    }

    /**
     * @param $text
     * @return document_message
     */
    function err($text)
    {
        return $this->err[] = new document_message($text, true);
    }

    /**
     * @param $text
     * @return document_message
     */
    function msg($text)
    {
        return $this->msg[] = new document_message($text);
    }

    /**
     * Переадресация на адрес, указанный в GET параметре return или в $default_url
     * @param string $default_url
     * @param int $timeout Время, через которое произойдет переадресация
     */
    function toReturn($default_url = '/', $timeout = 2)
    {
        if ($default_url instanceof url) {
            $url = $default_url->getUrl();
        } else {
            $url = $default_url;
        }

        if (!empty($_GET['return'])) {
            $url_return = new url($_GET['return']);
            if ($url_return->isInternalLink()) {
                $url = $url_return->getUrl();
            }
        }
        if ($timeout) {
            header('Refresh: ' . intval($timeout) . '; url=' . $url);
        } else {
            // если задержки быть не должно, то ничего на клиент не отправляем и работу скрипта прерываем
            header('Location: ' . $url);
            $this->outputed = true;
            exit;
        }
    }

    /**
     * Отображение страницы с ошибкой
     * @param string $err Текст ошибки
     */
    function access_denied($err)
    {
        if (isset($_GET['return']) && $url_return = new url($_GET['return'])) {
            if ($url_return->isInternalLink()) {
                header('Refresh: 2; url=' . $_GET['return']);
            }
        }
        $this->err($err);
        $this->output();
        exit;
    }

    /**
     * Формирование HTML документа и отправка данных браузеру
     * @global dcms $dcms
     */
    private function output()
    {
        global $dcms, $user_language_pack, $user;
        if ($this->outputed) {
            // повторная отправка html кода вызовет нарушение синтаксиса документа, да и вообще нам этого нафиг не надо
            return;
        }
        $this->outputed = true;
        header('Cache-Control: no-store, no-cache, must-revalidate', true);
        header('Expires: ' . date('r'), true);
        if ($this->last_modified) {
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", (int) $this->last_modified) . " GMT", true);
        }

        header('X-UA-Compatible: IE=edge', true); // отключение режима совместимости в осле
        header('Content-Type: text/html; charset=utf-8', true);

        $description = $this->description ?? $this->title;
        
        $content = ob_get_clean();
        $messages = $this->msg;
        $errors = $this->err;
        $document_generation_time = round(microtime(true) - TIME_START, 3); // время генерации страницы
        $lang = $user_language_pack;
        $title = $this->title;
        $is_main = IS_MAIN;
        $returns = $this->returns;
        $returns = array_reverse($returns);
        $actions = $this->actions;
        $tabs = $this->tabs;
        $url = URL;
        $current_user = json_encode(current_user::getInstance()->getCustomData(array('id', 'group', 'mail_new_count', 'friend_new_count', 'nick')));

        $left_column = $this->displaySectionBlade('left_column');
        $header = $this->displaySectionBlade('header');

        view($this->template, compact(
            'content','messages','errors','document_generation_time','lang','dcms','title',
            'is_main','returns','tabs','actions','user','current_user','word','left_column',
            'header','description'
        ));
    }

    /**
     * отображение содержимого блока темы
     * @param string $section
     */
    // public function displaySection($section)
    // {
    //     if ($section === $this->theme->getEchoSectionKey()) {
    //         echo $this->_echo_content;
    //     }
    //     $widgets = $this->theme->getWidgets($section);
    //     foreach ($widgets as $widget_name) {
    //         $widget = new widget(H . '/sys/widgets/' . $widget_name); // открываем
    //         $widget->display(); // отображаем
    //     }
    // }
    public function displaySectionBlade($section)
    {
        $content = '';
        $widgets = $this->theme->getWidgets($section);
        foreach ($widgets as $widget_name) {
            $widget = new widget(H . '/sys/widgets/' . $widget_name); // открываем
            $content .= $widget->displayBlade(); // отображаем
        }
        return $content;
    }

    /**
     * Очистка вывода
     * Тема оформления применяться не будет
     */
    function clean()
    {
        $this->outputed = true;
        ob_clean();
    }

    /**
     * То что срабатывает при exit
     */
    function __destruct()
    {
        $this->output();
    }
}