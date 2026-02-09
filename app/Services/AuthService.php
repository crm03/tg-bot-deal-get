<?php
namespace App\Services;

use GuzzleHttp\Client;
use App\Models\TelegramUser;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;


class AuthService
{
    private $telegramUserService;

    private $user;
    private $chatId;
    
    public function __construct()
    {
        $this->telegramUserService = app(TelegramUserService::class);
    }
    
    public function auth($update, $phoneNumber)
    {
        $message = $update->getMessage();
        $this->user = $message->getFrom();
        $this->chatId = $message->getChat()->getId();



        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => "Проверяю ваш номер: " . $phoneNumber,
        ]);

        $phoneNumber = str_replace('+', '', $phoneNumber);

        // Проверка есть ли юзер в Bitrix по номеру телефона
        $client = new Client();
        $webhook = config("services.bitrix.webhook");
        
        $response = $client->request('post', $webhook . 'user.get', [
            'json' => [
                "FILTER" => [
                    "ACTIVE" => true,
                    "WORK_PHONE" => "%$phoneNumber%"
                ],
            ],
            'http_errors' => false,
        ]);
    
        $responseBody = json_decode($response->getBody(), true);
        Log::debug("Bitrix user.get response: ", $responseBody);
        
        // Если пользователь найден в Bitrix
        if (isset($responseBody['result']) && is_array($responseBody['result']) && count($responseBody['result']) > 0) {
            // Добавляем номер телефона и авторизуем в TelegramUser
            $this->telegramUserService->addPhoneNumber($this->chatId, $phoneNumber);
            
            $telegramUser = $this->telegramUserService->makeTelegramUser($this->user);
        
            if (!$telegramUser) {
                Telegram::sendMessage([
                    'chat_id' => $this->chatId,
                    'text' => "❌ Ошибка при создании профиля. Попробуйте ещё раз.",
                ]);
                return;
            }
            $telegramUser->update([
                'is_authorized' => true,
            ]);
            
            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => "✅ Вы успешно авторизованы! Теперь можете запрашивать сделки.",
            ]);
        } else {
            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => "❌ Пользователь с номером " . $phoneNumber . " не найден в Bitrix.",
            ]);
        }        
    }

    public function logout($chatId)
    {
        try {
            $telegramUser = TelegramUser::where('chat_id', $chatId)->first();
            if ($telegramUser) {
                $telegramUser->resetAuth();
            }
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "🔒 Вы вышли из системы.",
            ]);
        } catch (\Exception $e) {
            Log::error('Logout failed for chat_id ' . $chatId . ': ' . $e->getMessage());
        }
    }
}
