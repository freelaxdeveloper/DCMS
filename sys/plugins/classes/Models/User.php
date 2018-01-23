<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\{files,files_file,groups};
use App\Models\ChatMini;

class User extends Model{
    protected $guarded = ['id'];

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
            if ($this->vk_id && $this->vk_first_name && $this->vk_last_name)
                return 'user.vk';
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
}