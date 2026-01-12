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

    /**
     * @throws \JsonException
     */
    public function getContent(): string
    {

        $message = json_encode([
            'from_email' => $this->fromEmail,
            'to_email' => $this->toEmail,
            'subject' => $this->subject,
            'body' => $this->body,
        ]);

        if ($message === false) {
            throw new \JsonException("Could not encode message to JSON:");
        }
        return $message;
    }
}
