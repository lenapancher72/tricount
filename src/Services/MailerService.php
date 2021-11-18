<?php

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($url, $recipientMail)
    {
        $email = (new Email())
            ->from('tricount@mail.com')
            ->to($recipientMail)
            ->subject('Tricount - Alerte nouvelle dépense')
            ->html('<a href=\''.$url.'\'>Voir la dépense</a>');

        $this->mailer->send($email);
    }
}