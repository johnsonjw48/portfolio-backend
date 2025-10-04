<?php

namespace App\Service;

use App\Entity\Contact;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ContactEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%env(MAILER_FROM_EMAIL)%')]
        private string $fromEmail,
        #[Autowire('%env(MAILER_TO_EMAIL)%')]
        private string $toEmail
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendContactNotification(Contact $contact): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($this->toEmail)
            ->replyTo($contact->getEmail())
            ->subject('Nouveau message de contact : ' . $contact->getSubject())
            ->html($this->getEmailBody($contact));

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendConfirmationEmail(Contact $contact): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($contact->getEmail())
            ->subject('Confirmation de réception de votre message')
            ->html($this->getConfirmationEmailBody($contact));

        $this->mailer->send($email);
    }

    private function getEmailBody(Contact $contact): string
    {
        return sprintf(
            '<html>
                <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                        <h2 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">
                            Nouveau message de contact
                        </h2>

                        <div style="margin: 20px 0;">
                            <p><strong style="color: #2c3e50;">Nom :</strong> %s</p>
                            <p><strong style="color: #2c3e50;">Email :</strong> <a href="mailto:%s">%s</a></p>
                            <p><strong style="color: #2c3e50;">Sujet :</strong> %s</p>
                        </div>

                        <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; margin: 20px 0;">
                            <h3 style="color: #2c3e50; margin-top: 0;">Message :</h3>
                            <p style="white-space: pre-wrap;">%s</p>
                        </div>

                        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #7f8c8d;">
                            <p>Ce message a été envoyé depuis le formulaire de contact de votre portfolio le %s</p>
                        </div>
                    </div>
                </body>
            </html>',
            htmlspecialchars($contact->getName()),
            htmlspecialchars($contact->getEmail()),
            htmlspecialchars($contact->getEmail()),
            htmlspecialchars($contact->getSubject()),
            nl2br(htmlspecialchars($contact->getMessage())),
            $contact->getCreatedAt()->format('d/m/Y à H:i')
        );
    }

    private function getConfirmationEmailBody(Contact $contact): string
    {
        return sprintf(
            '<html>
                <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                        <h2 style="color: #2c3e50; border-bottom: 2px solid #27ae60; padding-bottom: 10px;">
                            Merci pour votre message !
                        </h2>

                        <p>Bonjour <strong>%s</strong>,</p>

                        <p>Merci de m\'avoir contacté. J\'ai bien reçu votre message concernant :</p>

                        <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #27ae60; margin: 20px 0;">
                            <p style="margin: 0;"><strong style="color: #2c3e50;">%s</strong></p>
                        </div>

                        <p>Je reviendrai vers vous dans les plus brefs délais pour répondre à votre demande.</p>

                        <div style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                            <p style="margin: 0; color: #2c3e50;"><strong>Récapitulatif de votre message :</strong></p>
                            <p style="margin: 10px 0 0 0; white-space: pre-wrap; color: #555;">%s</p>
                        </div>

                        <p style="margin-top: 30px;">Cordialement,<br><strong>James Warren Johnson</strong></p>

                        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #7f8c8d;">
                            <p>Cet email a été envoyé automatiquement suite à votre demande de contact du %s</p>
                            <p style="margin-top: 10px;">Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer cet email.</p>
                        </div>
                    </div>
                </body>
            </html>',
            htmlspecialchars($contact->getName()),
            htmlspecialchars($contact->getSubject()),
            nl2br(htmlspecialchars($contact->getMessage())),
            $contact->getCreatedAt()->format('d/m/Y à H:i')
        );
    }
}
