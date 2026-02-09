<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    protected $fillable = [
        'chat_id',
        'first_name',
        'last_name',
        'phone_number',
        'is_authorized',
        'status',
    ];

    /**
     * Reset authentication-related fields for this Telegram user.
     * Clears phone number, authorization flag and status, then saves the model.
     *
     * @return $this
     */
    public function resetAuth()
    {
        $this->phone_number = null;
        $this->is_authorized = false;
        $this->status = null;
        $this->save();

        return $this;
    }

}
