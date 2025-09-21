<?php

namespace App\MessageHandler;

use App\Message\EmailMessage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
readonly class EmailMessageHandler
{

    public function __construct(private MailerInterface $mailer) {

    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(EmailMessage $message): void
    {

        $content = json_decode($message->getContent(), true);

        $email = (new Email())
            ->from($content['from_email'])
            ->to($content['to_email'])
            ->subject($content['subject'])
            ->text($content['body']);

        $this->mailer->send($email);
    }

}
