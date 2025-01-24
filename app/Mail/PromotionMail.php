<?php

namespace App\Mail;

use App\Models\Promotion;
use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromotionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Promotion $promotion;

    public Shop $shop;

    /**
     * Create a new message instance.
     */
    public function __construct($promotion, $shop)
    {
        $this->promotion = $promotion;
        $this->shop = $shop;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉New exclusive promotion at ' . $this->shop->name . ' !',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.promotion',
            with: [
                'promotion' => $this->promotion,
                'shop' => $this->shop,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    /*
    public function attachments(): array
    {
        $attachments = [];
        foreach ($this->promotion->images as $image) {
            $attachments[] = Attachment::fromPath(public_path('images/promotions/' . $image))
                ->as('promotion_' . $image)
                ->withMime('image/jpeg'); // Utilisation de withMime() à la place de mime()
        }
        return $attachments;
    }
    */

}
