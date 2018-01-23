Рефакторинг CMS системы DCMS

До выхода релиза миграции будут неоднократно изменятся что будет приводить к ошибкам, это временное явление, ждите релиза.

В терминале, с корня проекта прописать:
 - Для создания ключей
<b>openssl genrsa -out ./sys/key/private.pem 512</b>
<b>openssl rsa -in ./sys/key/private.pem -out sys/key/public.pem -outform PEM -pubout</b>
 - Для выполнения миграций
<b>php vendor/bin/phinx migrate -c config-phinx.php</b>
