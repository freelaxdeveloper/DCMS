<?php
namespace Dcms\Http\Controllers;

use Dcms\Core\Controller;
use Dcms\Models\User;
use App\App\App;
use App\{document,text,form,url,listing,misc,files};

class UserController extends Controller{

    public function view(int $user_id)
    {
        $this->doc->title = __('Анкета');

        if (!$user = User::find($user_id)) {
            return redirect()->back()->with('err', __('Пользователь не найден'));
        }
        view('pages.profile', compact('user'));
    }
}