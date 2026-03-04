<?php
namespace App\Controller;

use App\Entity\UtmVisit;
use App\Repository\UtmVisitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utm')]
class UtmApiController extends AbstractController
{
    public function __construct(private UtmVisitRepository $repo) {}

    #[Route('/track', name: 'utm', methods: ['POST'])]
    public function track(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $source = isset($data['utm_source']) ? (string)$data['utm_source'] : '';
        $medium = isset($data['utm_medium']) ? (string)$data['utm_medium'] : '';
        $campaign = isset($data['utm_campaign']) ? (string)$data['utm_campaign'] : '';

        if (!$source || !$medium || !$campaign) {
            return new JsonResponse(['error' => 'Missing UTM parameters'], 400);
        }

        $visit = new UtmVisit($source, $medium, $campaign);
        $this->repo->getEntityManager()->persist($visit);
        $this->repo->getEntityManager()->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}
