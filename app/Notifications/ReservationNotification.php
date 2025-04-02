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

    public function __construct(string $key, string $language = 'en')
    {
        $this->key = $key;
        $this->language = $language;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // Пробуем взять язык клиента из сессии, иначе fallback на установленный в Notification
        $language = session('customer_locale', $this->language);

        $template = NotificationTemplate::where('key', $this->key)
            ->where('language_code', $language)
            ->first();

        $message = $template ? $template->body : 'Default reservation message.';

        return [
            'message' => $message,
        ];
    }
}
