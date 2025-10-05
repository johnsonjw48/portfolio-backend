<?php

namespace App\Service;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Entity\Contact;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

class ContactEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        #[Autowire('%env(MAILER_FROM_EMAIL)%')]
        private string $fromEmail,
        #[Autowire('%env(MAILER_TO_EMAIL)%')]
        private string $toEmail
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendContactNotification(Contact $contact): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($this->toEmail)
            ->replyTo($contact->getEmail())
            ->subject('Nouveau message de contact : ' . $contact->getSubject())
            ->html($this->twig->render('emails/contact_notification.html.twig', [
                'contact' => $contact
            ]));

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     **/
    public function sendConfirmationEmail(Contact $contact): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($contact->getEmail())
            ->subject('Confirmation de réception de votre message')
            ->html($this->twig->render('emails/contact_confirmation.html.twig', [
                'contact' => $contact
            ]));

        $this->mailer->send($email);
    }
}
