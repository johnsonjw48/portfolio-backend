<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
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
        summary: 'Vérification de l\'état de l\'API et de la base de données',
        tags: ['Portfolio API']
    )]
    #[OA\Response(
        response: 200,
        description: 'API opérationnelle',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'ok'),
                new OA\Property(property: 'timestamp', type: 'integer', example: 1696435200),
                new OA\Property(
                    property: 'database',
                    properties: [
                        new OA\Property(property: 'connected', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Database connection OK')
                    ],
                    type: 'object'
                )
            ]
        )
    )]
    #[OA\Response(
        response: 503,
        description: 'Service indisponible',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'timestamp', type: 'integer', example: 1696435200),
                new OA\Property(
                    property: 'database',
                    properties: [
                        new OA\Property(property: 'connected', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Database connection failed')
                    ],
                    type: 'object'
                )
            ]
        )
    )]
    public function health(Connection $connection, LoggerInterface $logger): JsonResponse
    {
        $dbStatus = [
            'connected' => false,
            'message' => 'Database connection failed'
        ];

        try {
            // Test de connexion à la base de données
            $connection->executeQuery('SELECT 1');
            $dbStatus = [
                'connected' => true,
                'message' => 'Database connection OK'
            ];

            return $this->json([
                'status' => 'ok',
                'timestamp' => time(),
                'database' => $dbStatus
            ]);
        } catch (\Exception $e) {
            $logger->error('Health check failed: Database connection error', [
                'exception' => $e->getMessage()
            ]);

            return $this->json([
                'status' => 'error',
                'timestamp' => time(),
                'database' => $dbStatus
            ], 503);
        }
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
