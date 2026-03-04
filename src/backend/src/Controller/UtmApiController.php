<?php
namespace App\Controller;

use App\Entity\UtmVisit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utm')]
class UtmApiController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/track', name: 'utm_track', methods: ['POST'])]
    public function track(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $data */
        $data = json_decode($request->getContent(), true) ?? [];

        $source = (string) ($data['utm_source'] ?? '');
        $medium = (string) ($data['utm_medium'] ?? '');
        $campaign = (string) ($data['utm_campaign'] ?? '');

        if ($source === '' || $medium === '' || $campaign === '') {
            return new JsonResponse(['error' => 'Invalid UTM parameters'], 400);
        }

        $visit = new UtmVisit($source, $medium, $campaign);

        $this->em->persist($visit);
        $this->em->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}
