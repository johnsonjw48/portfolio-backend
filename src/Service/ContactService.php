<?php

namespace App\Service;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

class ContactService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getPaginatedContacts(int $page = 1, int $limit = 10): array
    {
        $page = max(1, $page);
        $limit = min(100, max(1, $limit));
        $offset = ($page - 1) * $limit;

        $repository = $this->entityManager->getRepository(Contact::class);

        $total = $repository->count([]);
        $contacts = $repository->findBy([], ['createdAt' => 'DESC'], $limit, $offset);

        $data = array_map(function (Contact $contact) {
            return [
                'id' => $contact->getId(),
                'name' => $contact->getName(),
                'email' => $contact->getEmail(),
                'subject' => $contact->getSubject(),
                'message' => $contact->getMessage(),
                'createdAt' => $contact->getCreatedAt()->format('c')
            ];
        }, $contacts);

        return [
            'success' => true,
            'data' => $data,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => (int) ceil($total / $limit)
            ]
        ];
    }
}
