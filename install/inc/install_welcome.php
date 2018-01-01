<?php
class install_welcome {
    function actions()
    {
        return true;
    }

    function form()
    {
        echo __("Добро пожаловать в мастер установки DCMS Seven");
        return true;
    }
}

?>
