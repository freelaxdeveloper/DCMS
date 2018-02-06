<?php
namespace Dcms\Core;

use App\document;
use App\App\App;

class Controller{

    protected $doc;

    public function __construct(){
        $this->doc = new document;

        if (!empty($_SESSION['err'])) {
            $this->doc->err($_SESSION['err']);
            unset($_SESSION['err']);
        }
        if (!empty($_SESSION['msg'])) {
            $this->doc->msg($_SESSION['msg']);
            unset($_SESSION['msg']);
        }
    }

    protected function access_denied(string $message = 'Доступ запрещен')
    {
        echo $message;
        exit;
    }
    # доступ только по токену
    protected function checkToken()
    {
        if (!App::user()->checkToken()) {
            $this->access_denied(__('Не верный token'));
        }
    }
}
