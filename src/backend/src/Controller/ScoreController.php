<?php
namespace App\Controller;

use App\Service\ScoreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ScoreController extends AbstractController
{
    public function __construct(private ScoreService $service) {}

    #[Route('/score', name: 'score', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = isset($data['username']) ? (string)$data['username'] : '';
        $score = isset($data['score']) ? (int)$data['score'] : 0;

        if (!$username || $score <= 0) {
            return new JsonResponse(['error' => 'Missing or invalid data'], 400);
        }

        return new JsonResponse($this->service->saveHighscore($username, $score));
    }

    #[Route('/top5', name: 'top5', methods: ['GET'])]
    public function top5(): JsonResponse
    {
        $top = $this->service->getTop5();
        $result = array_map(fn($s) => [
            'username' => $s->getUser()->getUsername(),
            'score' => $s->getValue()
        ], $top);

        return new JsonResponse($result);
    }
}
