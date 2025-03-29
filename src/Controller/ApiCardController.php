<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Routes for all about cards')]
class ApiCardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface        $logger
    )
    {
    }

    #[Route('/{uuid}', name: 'Show card', requirements: ['uuid' => '[0-9a-fA-F\-]{36}'], methods: ['GET'])]
    #[OA\Parameter(name: 'uuid', description: 'UUID of the card', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Get a card by UUID')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardShow(string $uuid): Response
    {
        $card = $this->entityManager->getRepository(Card::class)->findOneBy(['uuid' => $uuid]);
        if (!$card) {
            $this->logger->error('Card not found', ['uuid' => $uuid]);
            return $this->json(['error' => 'Card not found'], 404);
        }
        $this->logger->info('card by uuid', ['card' => $card]);
        return $this->json($card);
    }


    // src/Controller/ApiCardController.php

    #[Route('/all', name: 'List all cards', methods: ['GET'])]
    #[OA\Get(description: 'Return all cards in the database')]
    #[OA\Parameter(name: 'setCode', description: 'Filter by set code', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAll(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 20);
        $setCode = $request->query->get('setCode');

        $queryBuilder = $this->entityManager->getRepository(Card::class)
            ->createQueryBuilder('c')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        if ($setCode) {
            $queryBuilder->andWhere('c.setCode = :setCode')
                ->setParameter('setCode', $setCode);
        }

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        $cards = $queryBuilder->getQuery()->getResult();

        return $this->json([
            'cards' => $cards,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/search', name: 'Search cards', methods: ['GET'])]
    #[OA\Get(description: 'Search cards by name')]
    #[OA\Parameter(name: 'name', description: 'Name of the card', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'setCode', description: 'Filter by set code', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Search results')]
    #[OA\Response(response: 400, description: 'Invalid search query')]
    public function searchCards(Request $request): Response
    {
        $name = $request->query->get('name');
        $setCode = $request->query->get('setCode');

        if (strlen($name) < 3) {
            return $this->json(['error' => 'Search query must be at least 3 characters long'], 400);
        }

        $queryBuilder = $this->entityManager->getRepository(Card::class)
            ->createQueryBuilder('c')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->setMaxResults(20);

        if ($setCode) {
            $queryBuilder->andWhere('c.setCode = :setCode')
                ->setParameter('setCode', $setCode);
        }

        $cards = $queryBuilder->getQuery()->getResult();

        return $this->json($cards);
    }

    // src/Controller/ApiCardController.php

    #[Route('/setcodes', name: 'List all set codes', methods: ['GET'])]
    #[OA\Get(description: 'Return all unique set codes')]
    #[OA\Response(response: 200, description: 'List all set codes')]
    public function listSetCodes(): Response
    {
        $setCodes = $this->entityManager->getRepository(Card::class)
            ->createQueryBuilder('c')
            ->select('DISTINCT c.setCode')
            ->getQuery()
            ->getResult();

        return $this->json($setCodes);
    }
}
