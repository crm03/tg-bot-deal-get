<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
class DealService
{
    public function formatDeal($dealData)
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

    public function getDeal(int $id)
    {
        $webhook = config("services.bitrix.webhook");
        $response = Http::get($webhook . 'crm.deal.get', [
            'id' => $id,
        ]);
        return $response->json();
    }
}
