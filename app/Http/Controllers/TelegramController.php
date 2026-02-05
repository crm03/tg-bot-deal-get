<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    private $bitrix_url;

    public function __construct(){
        $this->bitrix_url = config("services.bitrix_url.link");
        if (!str_ends_with($this->bitrix_url, '/')) {
            $this->bitrix_url .= '/';
        }
    }
    
    private function getDeal(int $id)
    {
        $response = Http::get($this->bitrix_url . 'crm.deal.get', ['id' => $id]);
        Log::debug("Bitrix URL: " . $this->bitrix_url . 'crm.deal.get', ['id' => $id]);
        return $response->json();
    }

    private function formatDeal($dealData)
    {
        if (!isset($dealData['result'])) {
            return 'Сделка не найдена';
        }
        
        $deal = $dealData['result'];
        
        $text = "📋 <b>Сделка №" . ($deal['ID'] ?? 'N/A') . "</b>\n\n";
        $text .= "<b>Название:</b> " . ($deal['TITLE'] ?? 'N/A') . "\n";
        $text .= "<b>Тип:</b> " . ($deal['TYPE_ID'] ?? 'N/A') . "\n";
        $text .= "<b>Статус:</b> " . ($deal['STAGE_ID'] ?? 'N/A') . "\n";
        $text .= "<b>Контакт:</b> " . ($deal['CONTACT_ID'] ?? 'N/A') . "\n";
        $text .= "<b>Сумма:</b> " . ($deal['OPPORTUNITY'] ?? '0') . " " . ($deal['CURRENCY_ID'] ?? 'UAH') . "\n";
        $text .= "<b>Начало:</b> " . ($deal['BEGINDATE'] ?? 'N/A') . "\n";
        $text .= "<b>Закрытие:</b> " . ($deal['CLOSEDATE'] ?? 'N/A') . "\n";
        $text .= "<b>Создано:</b> " . ($deal['DATE_CREATE'] ?? 'N/A') . "\n";
        $text .= "<b>Обновлено:</b> " . ($deal['DATE_MODIFY'] ?? 'N/A') . "\n";
        
        return $text;
    }

    public function handle(Request $request)
    {
        Log::info(print_r($request->all(), true));
        $update = Telegram::getWebhookUpdate();
        
        if ($update) {
            $message = $update->getMessage();
            $user = $message->getFrom();
            $chatId = $message->getChat()->getId();
            $text = $message->getText();
            
            $dealId = $this->getDeal((int)$text  );
            
            $dealText = $this->formatDeal($dealId);
            
            Telegram::sendMessage([
                'chat_id' => 2093803459, // $chatId,
                'text' => $dealText,
                'parse_mode' => 'HTML',
            ]);
        }

        
    }
}