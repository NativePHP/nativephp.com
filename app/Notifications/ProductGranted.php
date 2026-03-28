<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductGranted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("You've been granted access to {$this->product->name}!")
            ->markdown('mail.product-granted', [
                'product' => $this->product,
                'url' => route('products.show', $this->product),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "You've been granted access to {$this->product->name}!",
            'body' => "You now have access to {$this->product->name}.",
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
        ];
    }
}
