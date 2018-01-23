Рефакторинг CMS системы DCMS<br><br>

До выхода релиза миграции будут неоднократно изменятся что будет приводить к ошибкам, это временное явление, ждите релиза.<br><br>

В терминале, с корня проекта прописать:<br>
 - Для создания ключей<br>
<b>openssl genrsa -out ./sys/key/private.pem 512</b><br>
<b>openssl rsa -in ./sys/key/private.pem -out sys/key/public.pem -outform PEM -pubout</b><br><br>
 - Для выполнения миграций<br>
<b>php vendor/bin/phinx migrate -c config-phinx.php</b><br><br>
