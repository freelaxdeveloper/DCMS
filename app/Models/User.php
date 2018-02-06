<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use App\{files,files_file,groups};
use Dcms\Models\{ChatMini,Browser};
use App\App\App;

class User extends Model{
    protected $guarded = ['id'];
    protected $hidden = ['password', 'token'];

    protected $casts = [
        'info' => 'array',
    ];

    public function getIconAttribute(){
        // система
        if ($this->group === 6 && $this->id === 0) {
            return 'system';
        }
        // забаненый пользователь
        if ($this->is_ban) {
            return 'shit';
        }
        // администратор
        if ($this->group >= 2) {
            return 'admin.' . $this->sex;
        }
        // пользователь
        if ($this->group) {
            return 'user.' . $this->sex;
        }
        // гость
        return 'guest';
    }

    public function chatMini()
    {
        return $this->hasMany(ChatMini::class, 'id_user');
    }
    function getGroupNameAttribute()
    {
        return groups::name($this->group);
    }

    function getAvatarAttribute()
    {
        $avatar_file_name = $this->id . '.jpg';
        $avatars_path = FILES . '/.avatars'; // папка с аватарами
        $avatars_dir = new files($avatars_path);
        if ($avatars_dir->is_file($avatar_file_name)) {
            $avatar = new files_file($avatars_path, $avatar_file_name);
            return $avatar->getScreen($max_width, 0);
        }
    }

    public function getIsWriteableAttribute(): bool
    {
        // if ($this->_is_ban())
        //     return false;

        global $dcms;
        if (!$dcms->user_write_limit_hour) {
            // ограничение не установлено
            return true;
        } elseif ($this->group >= 2) {
            // пользователь входит в состав администрации
            return true;
        } elseif ($this->reg_date < TIME - $dcms->user_write_limit_hour * 3600) {
            // пользователь преодолел ограничение
            return true;
        } else {
            return false;
        }
    }

    public function getItemsPerPageAttribute()
    {
        return 5;
    }


    # проверяем токен
    public function checkToken(): bool
    {
        if (!$this->id) {
            return false;
        }
        if (!isset($_GET['token']) && !isset($_POST['token'])) {
            return false;
        }
        $token = $_GET['token'] ?? $_POST['token'] ?? null;
        if ($token == $this->url_token) {
            $this->updateToken();
            return true;
        }
        return false;
    }
    # обновляем токен
    public function updateToken()
    {
        $this->url_token = App::generateToken(32);
        return $this->save();
    }

}