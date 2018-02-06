<?php
namespace Dcms\Models;

use Illuminate\Database\Eloquent\Model;
use Dcms\Models\{Browser,User};
use App\App\App;

class GuestOnline extends Model {
    protected $table = 'guest_online';
    protected $fillable = ['ip_long','is_robot','browser','browser_ua','time_start','time_last','domain','request','conversions'];

    public function getInfoAttribute()
    {
        global $dcms;
        $content = "Переходов: {$this->conversions}\n";
        if (App::user()->group || $this->ip_long == $dcms->ip_long) {
            $content .= 'IP:' . long2ip($this->ip_long) . "\n";
        }
        $content .= "Браузер: {$this->browser}\n";
        if (App::user()->group > 1 && $this->browser_ua != '') {
            $content .= "User-Agent: {$this->browser_ua}\n";
        }
        return $content;
    }
}