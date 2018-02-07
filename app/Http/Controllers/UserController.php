<?php
namespace Dcms\Http\Controllers;

use Dcms\Core\Controller;
use Dcms\Models\{User,UserOnline};
use App\App\{App,Authorize};
use App\{document,text,form,url,listing,misc,files,menu_ini};

class UserController extends Controller{

    public function view(int $user_id)
    {
        $this->doc->title = __('Анкета');

        if (!$user = User::find($user_id)) {
            return redirect()->back()->with('err', __('Пользователь не найден'));
        }
        view('pages.profile', compact('user'));
    }

    public function menu()
    {
        $this->doc->title = __('Личное меню');

        $menu = new menu_ini('user');
        $menu->display();
    }

    public function exit()
    {
        $this->checkToken();

        $userOnline = UserOnline::where('id_user', App::user()->id)->first();
        $userOnline->delete();

        Authorize::exit();

        return redirect()->route('home')->with('msg', __('Авторизация успешно сброшена'));
    }
}