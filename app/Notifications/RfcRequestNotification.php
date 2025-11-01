<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Rfc;
use App\Models\Startup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

final class RfcRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Startup $startup,
        private Rfc $rfc,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        \Illuminate\Support\Facades\Config::set('app.url', 'http://127.0.0.1:8000/');
        $signedUrl = URL::signedRoute('rfc.respond', ['rfc' => $this->rfc], now()->addDays(7));

        return (new MailMessage)
            ->subject("Share your feedback about {$this->startup->name}")
            ->greeting($this->rfc->customer_name ? "Dear {$this->rfc->customer_name}," : 'Hi,')
            ->line("We are contacting you on behalf of {$this->startup->name}, who has requested that we collect feedback regarding your experience with them.")
            ->line(config('app.name') . ' is an independent third-party platform that facilitates authentic customer reviews.')
            ->line("Please click the button below to drop a comment for {$this->startup->name}.")
            ->action('Provide Feedback', $signedUrl)
            ->line('Thank you for your feedback!');
    }

    public function toArray(): array
    {
        return [
            //
        ];
    }
}
