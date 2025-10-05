<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Event\ContactSubmittedEvent;
use App\Form\ContactType;
use App\Handler\ContactFormHandler;
use App\Service\ContactService;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
#[OA\Tag(name: 'Contact')]
class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['POST'])]
    #[OA\Post(
        path: '/api/contact',
        summary: 'Envoyer un message de contact',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: ContactType::class))
        ),
        tags: ['Contact']
    )]
    #[OA\Response(
        response: 201,
        description: 'Message envoyé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Votre message a été envoyé avec succès')
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Données invalides',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: false),
                new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string'))
            ]
        )
    )]
    public function contact(
        Request $request,
        ContactFormHandler $formHandler,
        EventDispatcherInterface $eventDispatcher
    ): JsonResponse {
        $result = $formHandler->handle($request);

        if (!$result['success']) {
            return $this->json([
                'success' => false,
                'errors' => $result['errors']
            ], 400);
        }

        try {
            // Dispatch de l'événement qui va gérer la persistence et les emails
            $event = new ContactSubmittedEvent($result['contact']);
            $eventDispatcher->dispatch($event, ContactSubmittedEvent::NAME);

            // Retour de succès
            return $this->json([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès'
            ], 201);
        } catch (Exception $e) {
            // En cas d'erreur dans les subscribers
            return $this->json([
                'success' => false,
                'errors' => ['Une erreur est survenue lors de l\'envoi du message']
            ], 500);
        }
    }

    #[Route('/contacts', name: 'contacts_list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/contacts',
        summary: 'Récupérer la liste des messages de contact',
        tags: ['Contact']
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Numéro de page',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1)
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Nombre d\'éléments par page',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 10, maximum: 100)
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des messages de contact',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Contact::class))
                ),
                new OA\Property(
                    property: 'meta',
                    properties: [
                        new OA\Property(property: 'total', type: 'integer', example: 50),
                        new OA\Property(property: 'page', type: 'integer', example: 1),
                        new OA\Property(property: 'limit', type: 'integer', example: 10),
                        new OA\Property(property: 'totalPages', type: 'integer', example: 5)
                    ],
                    type: 'object'
                )
            ]
        )
    )]
    public function list(Request $request, ContactService $contactService): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);
        return $this->json($contactService->getPaginatedContacts($page, $limit));
    }
}
