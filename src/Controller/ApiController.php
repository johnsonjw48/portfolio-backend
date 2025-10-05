<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
#[OA\Tag(name: 'Portfolio API')]
class ApiController extends AbstractController
{
    #[Route('/health', name: 'health', methods: ['GET'])]
    #[OA\Get(
        path: '/api/health',
        summary: 'Vérification de l\'état de l\'API',
        tags: ['Portfolio API']
    )]
    #[OA\Response(
        response: 200,
        description: 'API opérationnelle',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'ok'),
                new OA\Property(property: 'timestamp', type: 'integer', example: 1696435200)
            ]
        )
    )]
    public function health(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'timestamp' => time()
        ]);
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[OA\Get(
        path: '/api',
        summary: 'Point d\'entrée de l\'API',
        tags: ['Portfolio API']
    )]
    #[OA\Response(
        response: 200,
        description: 'Informations de l\'API',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Welcome to Portfolio API'),
                new OA\Property(property: 'version', type: 'string', example: '1.0.0')
            ]
        )
    )]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to Portfolio API',
            'version' => '1.0.0'
        ]);
    }
}
