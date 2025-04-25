<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;

class ReservationNotification extends Notification
{
    use Queueable;

    protected $key;
    protected $language;
    protected $byAdmin;

    public function __construct(string $key, string $language = 'en', bool $byAdmin = false)
    {
        $this->key = $key;
        $this->language = $language;
        $this->byAdmin = $byAdmin;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $language = session('customer_locale', $this->language);

        // Используем другой ключ, если действие сделал админ
        $finalKey = $this->byAdmin ? "{$this->key}_by_admin" : $this->key;

        $template = NotificationTemplate::where('key', $finalKey)
            ->where('language_code', $language)
            ->first();

        $message = $template ? $template->body : 'Default reservation message.';

        return [
            'message' => $message,
        ];
    }
}

