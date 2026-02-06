<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use Telegram\Bot\Laravel\Facades\Telegram;
use App\Services\AuthService;
use App\Services\DealService;
use App\Services\TelegramUserService;
use App\Models\TelegramUser;

class TelegramController extends Controller
{
    private $bitrix_url;
    private $authService;
    private $dealService;
    private $telegramUserService;
    public function __construct(){
        $this->bitrix_url = config("services.bitrix.base_url");
        $this->authService = app(AuthService::class);
        $this->dealService = app(DealService::class);
        $this->telegramUserService = app(TelegramUserService::class);
    }
    

    public function handle(Request $request)
    {
        $update = Telegram::getWebhookUpdate();
        if ($update) {
            $message = $update->getMessage();
            $chatId = $message->getChat()->getId();
            $text = $message->getText();
            
            $telegramUser = TelegramUser::where('chat_id', $chatId)->first();

            if (is_string($text) && trim($text) === '/logout') {
                $this->authService->logout($chatId);
                return;
            }

            if (!$telegramUser || !$telegramUser->is_authorized) {
                // Пользователь не авторизован - проверяем, похож ли текст на номер телефона
                if (preg_match('/^\+?\d{10,}$/', $text)) {
                    // Текст похож на номер телефона - авторизуем
                    $this->authService->auth($update, $text);
                } else {
                    // Просим номер телефона
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "👤 Пожалуйста, введите ваш номер телефона (например: +380633333333 или 380633333333)",
                    ]);
                }
                return;
            }
            
            // Пользователь авторизован проверка сделки
            if (is_numeric($text)) {
                $dealId = $this->dealService->getDeal((int)$text);
                $dealText = $this->dealService->formatDeal($dealId);
                
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => $dealText,
                    'parse_mode' => 'HTML',
                ]);
            } else {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ Пожалуйста, введите ID сделки (число)",
                ]);
            }
        }
    }
}