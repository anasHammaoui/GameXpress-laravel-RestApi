<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessfulNotification extends Notification
{
    use Queueable;

    protected $paymentIntent;
    protected $products;

    /**
     * Create a new notification instance.
     */
    public function __construct($paymentIntent, $products)
    {
        $this->paymentIntent = $paymentIntent;
        $this->products = $products;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
                    ->subject('Payment Successful - Your Order has been Confirmed.')
                    ->line('Thank you for your purchase! Your payment has been successfully processed.')
                    ->line('Order Details:');

        // foreach ($this->products as $product) 
        // {
        //     $mailMessage->line("{$product['name']} - Quantity : {$product['quantity']}");
        // }

        $mailMessage->line('Montant Total : ' . ($this->paymentIntent->amount_received / 100) . ' ' . strtoupper($this->paymentIntent->currency))
            ->line('Transaction ID : ' . $this->paymentIntent->id)
            ->line('Date : ' . date('Y-m-d H:i:s', $this->paymentIntent->created))
            ->line('Thank you for trusting our platform. We hope to see you again soon!');

        return $mailMessage;
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
