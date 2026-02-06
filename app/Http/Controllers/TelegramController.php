<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use Telegram\Bot\Laravel\Facades\Telegram;
use App\Services\AuthService;
use App\Services\DealService;

class TelegramController extends Controller
{
    private $bitrix_url;

    public function __construct(){
        $this->bitrix_url = config("services.bitrix_url.link");
        if (!str_ends_with($this->bitrix_url, '/')) {
            $this->bitrix_url .= '/';
        }
    }
    

    public function handle(Request $request)
    {
        $authService = app(AuthService::class);
        $dealService = app(DealService::class);

        $update = Telegram::getWebhookUpdate();
        Log::debug($update);
        if ($update) {
            $message = $update->getMessage();
            $user = $message->getFrom();
            $chatId = $message->getChat()->getId();
            $text = $message->getText();
            $phoneNumber = $user->getPhoneNumber();

            $authService->auth($update);
            
            $dealId = $dealService->getDeal((int)$text);
            
            $dealText = $dealService->formatDeal($dealId);
            
            Telegram::sendMessage([
                'chat_id' => 2093803459, // $chatId,
                'text' => $dealText,
                'parse_mode' => 'HTML',
            ]);
        }

        
    }
}