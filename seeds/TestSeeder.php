<?php
use App\Migration\Seed;
use App\Models\ChatMini;

class TestSeeder extends Seed
{
    public function run()
    {
        $message = new ChatMini;
        $message->time = 585858585;
        $message->id_user = 1;
        $message->message = 'Test';
        $message->save();
    }
}
