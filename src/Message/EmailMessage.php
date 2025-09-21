<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage()]
class EmailMessage
{
    public function __construct(
        private string $fromEmail,
        private string $toEmail,
        private string $subject,
        private string $body,
    ) {
    }

    public function getContent(): string
    {
        return json_encode([
            'from_email' => $this->fromEmail,
            'to_email' => $this->toEmail,
            'subject' => $this->subject,
            'body' => $this->body,
        ]);
    }
}
