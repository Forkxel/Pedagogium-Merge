<?php

namespace App\Controller;

use App\Service\GameSessionService;
use App\Service\ScoreService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use App\Utils\TypeCast;

#[Route('/api/score')]
class ScoreController
{
    public function __construct(
        private ScoreService $scoreService,
        private UserService $userService,
        private GameSessionService $gameSessionService,
        private RateLimiterFactory $scoreSubmitLimiter
    ) {}

    #[Route('/start', name: 'score_start', methods: ['POST'])]
    public function start(Request $request): JsonResponse
    {
        $userId = $request->getSession()->get('user_id');

        if (!is_int($userId)) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $user = $this->userService->findById($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $gameSession = $this->gameSessionService->create($user, $request->getClientIp());

        return new JsonResponse([
            'gameToken' => $gameSession->getToken(),
        ]);
    }

    #[Route('/submit', name: 'score_submit', methods: ['POST'])]
    public function submit(Request $request): JsonResponse
    {
        $userId = $request->getSession()->get('user_id');

        if (!is_int($userId)) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $user = $this->userService->findById($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $limit = $this->scoreSubmitLimiter
            ->create($request->getClientIp() ?? 'anon')
            ->consume();

        if (!$limit->isAccepted()) {
            return new JsonResponse(['error' => 'Too many requests'], 429);
        }

        $decoded = json_decode($request->getContent(), true);

        if (!is_array($decoded)) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        /** @var array<string, mixed> $data */
        $data = $decoded;

        $gameToken = trim(TypeCast::toString($data['gameToken'] ?? ''));
        $score = TypeCast::toInt($data['score'] ?? 0);

        if ($gameToken === '') {
            return new JsonResponse(['error' => 'Missing game token'], 400);
        }

        $gameSession = $this->gameSessionService->findByToken($gameToken);
        if (!$gameSession) {
            return new JsonResponse(['error' => 'Invalid game token'], 400);
        }

        $validation = $this->gameSessionService->validateForSubmit(
            $gameSession,
            $user,
            $score,
            $request->getClientIp()
        );

        if ($validation['ok'] !== true) {
            return new JsonResponse(['error' => $validation['error']], 400);
        }

        $result = $this->scoreService->saveHighscoreForUser($user, $score);
        $this->gameSessionService->markUsed($gameSession);

        return new JsonResponse($result);
    }

    #[Route('/top5', name: 'score_top5', methods: ['GET'])]
    public function top5(): JsonResponse
    {
        $scores = $this->scoreService->getTop5();

        $output = array_map(fn($s) => [
            'username' => $s->getUser()->getUsername(),
            'score' => $s->getValue(),
        ], $scores);

        return new JsonResponse($output);
    }
}