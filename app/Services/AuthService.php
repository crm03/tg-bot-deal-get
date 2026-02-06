<?php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;


class AuthService
{
    public function auth($update)
    {
        $message = $update->getMessage();
        $user = $message->getFrom();
        $chatId = $message->getChat()->getId();
        $phoneNumber = $user->getPhoneNumber();
        

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Ваш номер: " . $phoneNumber,
        ]);

        $phoneNumber = str_replace('+', '', $phoneNumber);

        //is user from bitrix 
        $client = new Client();

        $response = $client->request('post', config("services.bitrix_url.link") . 'user.get', [
            'body' => [
                    "FILTER" => [
                        "ACTIVE" => true,
                        "WORK_PHONE" => "%$phoneNumber%"
                    ],
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
        ]);
    
        Log::debug("Bitrix user.get response: " . $response->getBody());
    }


}
