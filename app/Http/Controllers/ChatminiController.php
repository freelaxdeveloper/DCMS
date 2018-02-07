<?php
namespace Dcms\Http\Controllers;

use Dcms\Core\Controller;
use App\{pages,text,misc,listing,form,antiflood,is_valid};
use Dcms\Models\{ChatMini,User};
use App\App\App;

class ChatminiController extends Controller{

    /**
     * список сообщений
     */
    public function messagesList()
    {
        $this->doc->title = __('Мини чат');

        if (App::user()->group >= GROUP_SMODER)
            $this->doc->act(__('Удаление сообщений'), route('chat:drop', ['?token=' . App::user()->url_token]));

        if (App::user()->group) {
            $message_form = '';
            $form = new form(route('chatmini:send'));
            $form->hidden('token', antiflood::getToken('chat_mini'));
            $form->textarea('message', __('Сообщение'), $message_form, true);
            $form->button(__('Отправить'), 'send', false);
            $form->display();
        }

        $pages = new pages(ChatMini::count());

        $messages = ChatMini::orderBy('id', 'DESC')->get()->forPage($pages->this_page, App::user()->items_per_page);

        view('chat_mini.messages', compact('messages'));

        $pages->display('?'); // вывод страниц
    }
    /**
     * отправка сообщения
     */
    public function send()
    {
        $v = new \Valitron\Validator($_POST);
        $v->rule('required', ['token', 'message'])->message('{field} - обязательно для заполнения');
        $v->labels([
            'token' => 'Токен',
            'message' => 'Сообщение',
        ]);
        if (!antiflood::useToken($_POST['token'], 'chat_mini')) {
            $v->error('error_token', 'Не удалось отправить сообщение');
        }
        if ($mat = is_valid::mat($_POST['message'])) {
            $v->error('mat', __('Обнаружен мат: %s', $mat));
        }
        if (!$v->validate()) {
            return redirect()->back()->with('err', $v->errors());
        }
        App::user()->increment('balls');

        $comment = new ChatMini(['message' => $_POST['message']]);
        App::User()->chatMini()->save($comment);

        return redirect()->back()->with('msg', 'Сообщение отравлено');
    }
    /**
     * очистка сообщений
     */
    public function drop()
    {
        $this->checkToken();

        ChatMini::truncate();
        return redirect()->route('chat')->with('msg', __('Сообщения успешно удалены'));
    }
    /**
     * удаление сообщений
     */
    public function delete(int $message_id)
    {
        $this->checkToken();

        if (!$message = ChatMini::find($message_id)) {
            return redirect()->back()->with('err', __('Сообщение не найдено'));
        }
        $message->delete();
        return redirect()->route('chat')->with('msg', __('Сообщение успешно удалено'));
    }
    /**
     * список действий с сообщением
     */
    public function actions(int $message_id)
    {
        $this->doc->title = __('Действия');

        if (!$message = ChatMini::find($message_id)) {
            return redirect()->back()->with('err', __('Сообщение не найдено'));
        }
        $this->doc->ret(__('Вернутся'), route('chat'));

        $listing = new listing;

        $ank = User::find($message->id_user);
        
        $post = $listing->post();
        $post->title = $ank->login;
        $post->content = text::toOutput($message->message);
        $post->time = misc::when($message->time);
        $post->icon($ank->icon);
        
        $post = $listing->post();
        $post->title = __('Посмотреть анкету');
        $post->icon('ank_view');
        $post->url = route('user:view', [$ank->id]);
        
        
        /* if (App::user()->group) {
            $post = $listing->post();
            $post->title = __('Ответить');
            $post->icon('reply');
            $post->url = 'index.php?message=' . $message->id . '&amp;reply';
        
            $post = $listing->post();
            $post->title = __('Цитировать');
            $post->icon('quote');
            $post->url = 'index.php?message=' . $message->id . '&amp;quote';
        } */
        
        if (App::user()->group >= GROUP_MODER) {
            $post = $listing->post();
            $post->title = __('Удалить сообщение');
            $post->icon('delete');
            $post->url = route('chat:delete', [$message->id, '?token=' . App::user()->url_token]);
        }
        
        
        $listing->display();
    }
}