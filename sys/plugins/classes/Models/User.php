<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class User extends Model{
    public $timestamps = false;
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
}