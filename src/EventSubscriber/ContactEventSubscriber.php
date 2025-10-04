<?php

namespace App\EventSubscriber;

use App\Event\ContactSubmittedEvent;
use App\Service\ContactEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactEmailService $emailService,
        private LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactSubmittedEvent::NAME => [
                ['onContactSubmittedPersist', 10],
                ['onContactSubmittedNotify', 5],
                ['onContactSubmittedConfirm', 0],
            ],
        ];
    }

    public function onContactSubmittedPersist(ContactSubmittedEvent $event): void
    {
        try {
            $contact = $event->getContact();

            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            $this->logger->info('Contact persisted successfully', [
                'contact_id' => $contact->getId(),
                'email' => $contact->getEmail()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to persist contact', [
                'error' => $e->getMessage(),
                'email' => $event->getContact()->getEmail()
            ]);
            throw $e;
        }
    }

    public function onContactSubmittedNotify(ContactSubmittedEvent $event): void
    {
        try {
            $this->emailService->sendContactNotification($event->getContact());

            $this->logger->info('Notification email sent to admin', [
                'contact_id' => $event->getContact()->getId()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send notification email', [
                'error' => $e->getMessage(),
                'contact_id' => $event->getContact()->getId()
            ]);
            // On ne throw pas pour ne pas bloquer l'email de confirmation
        }
    }

    public function onContactSubmittedConfirm(ContactSubmittedEvent $event): void
    {
        try {
            $this->emailService->sendConfirmationEmail($event->getContact());

            $this->logger->info('Confirmation email sent to user', [
                'contact_id' => $event->getContact()->getId(),
                'email' => $event->getContact()->getEmail()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send confirmation email', [
                'error' => $e->getMessage(),
                'contact_id' => $event->getContact()->getId()
            ]);
            // On ne throw pas, le contact est déjà enregistré
        }
    }
}
