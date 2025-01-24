<?php

namespace App\Notifications;

use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PromotionNotification extends Notification
{
    use Queueable;

    protected Promotion $promotion;

    /**
     * Create a new notification instance.
     */
    public function __construct($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->promotion->title,
            'shop' => $this->promotion->shop->name,
            'object' => '🎉 New exclusive promotion at ' . $this->promotion->shop->name . '!',
            'message-1' => 'Hello, \n The Shop ' . $this->promotion->shop->name . ' offers you an exceptional offer: \n 💸 ' . $this->promotion->discount_percentage . '% off on a selection of items!',
            'validity' => '📅 Promotion valid from '. \Carbon\Carbon::parse($this->promotion->starts_at)->format('d/m/Y') . ' At ' . \Carbon\Carbon::parse($this->promotion->ends_at)->format('d/m/Y'),
            'message-2' => 'Don\'t miss this opportunity!',
            'promo_code' => 'Use promo code: ' . $this->promotion->promo_code,
            'message-3' => '👉 Visit our store now to take advantage of the offer.',
            'conclusion' => 'Kind regards, The ' . $this->promotion->shop->name . ' team!'
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }
}
