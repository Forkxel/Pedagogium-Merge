<?php

namespace App\Controller\Admin;

use App\Repository\UtmVisitRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/utm')]
class UtmAdminController
{
    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function stats(UtmVisitRepository $repo): JsonResponse
    {
        $bySource = $repo->createQueryBuilder('u')
            ->select('u.utmSource AS utmSource, COUNT(u.id) AS visits, COUNT(DISTINCT u.ipAddress) AS uniqueIps')
            ->groupBy('u.utmSource')
            ->orderBy('visits', 'DESC')
            ->getQuery()
            ->getResult();

        $totals = $repo->createQueryBuilder('u')
            ->select('COUNT(u.id) AS totalVisits, COUNT(DISTINCT u.ipAddress) AS totalUniqueIps')
            ->getQuery()
            ->getOneOrNullResult();

        return new JsonResponse([
            'totals' => [
                'totalVisits' => (int) ($totals['totalVisits'] ?? 0),
                'totalUniqueIps' => (int) ($totals['totalUniqueIps'] ?? 0),
            ],
            'bySource' => array_map(static fn(array $row) => [
                'utmSource' => $row['utmSource'],
                'visits' => (int) $row['visits'],
                'uniqueIps' => (int) $row['uniqueIps'],
            ], $bySource),
        ]);
    }
}