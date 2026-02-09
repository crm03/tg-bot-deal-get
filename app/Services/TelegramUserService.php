<?php

namespace App\Services;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;

class TelegramUserService
{
    public function makeTelegramUser($user){
        if($user === null || $user->getId() === null || $user->getFirstName() === null){
            return null;
        }
        $telegramUser = TelegramUser::firstOrCreate(
            [
                'chat_id' => $user->getId()
            ],
            [
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName() ?? null,
            ]
        );
        return $telegramUser;
    }

    public function addPhoneNumber($userId, $phoneNumber){
        try{
            TelegramUser::where('chat_id', $userId)->update(['phone_number' => $phoneNumber]); 
        }
        catch(\Exception $e){
            Log::error("Failed to update phone number for user $userId: " . $e->getMessage());
        }
    }
}
