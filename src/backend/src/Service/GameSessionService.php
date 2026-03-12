<?php

namespace App\Service;

use App\Entity\GameSession;
use App\Entity\User;
use App\Repository\GameSessionRepository;
use Doctrine\ORM\EntityManagerInterface;

class GameSessionService
{
    private const MIN_GAME_SECONDS = 15;
    private const MAX_GAME_SECONDS = 7200;
    private const ABSOLUTE_SCORE_CAP = 1000000;
    private const MAX_SCORE_PER_SECOND = 20;
    private const SCORE_BASE_BUFFER = 100;

    public function __construct(
        private GameSessionRepository $gameSessionRepo,
        private EntityManagerInterface $em,
    ) {}

    public function create(User $user, ?string $ipAddress): GameSession
    {
        $token = bin2hex(random_bytes(32));

        $session = new GameSession($user, $token, $ipAddress);

        $this->em->persist($session);
        $this->em->flush();

        return $session;
    }

    public function findByToken(string $token): ?GameSession
    {
        return $this->gameSessionRepo->findOneByToken($token);
    }

    /**
     * @return array{ok: true}|array{ok: false, error: string}
     */
    public function validateForSubmit(GameSession $session, User $user, int $score, ?string $ipAddress): array
    {
        if ($session->isUsed()) {
            return ['ok' => false, 'error' => 'Game token already used'];
        }

        if ($session->getUser()->getId() !== $user->getId()) {
            return ['ok' => false, 'error' => 'Game token does not belong to user'];
        }

        if ($score <= 0 || $score > self::ABSOLUTE_SCORE_CAP) {
            return ['ok' => false, 'error' => 'Invalid score'];
        }

        if ($session->getIpAddress() !== null && $ipAddress !== null && $session->getIpAddress() !== $ipAddress) {
            return ['ok' => false, 'error' => 'IP mismatch'];
        }

        $duration = time() - $session->getStartedAt()->getTimestamp();

        if ($duration < self::MIN_GAME_SECONDS) {
            return ['ok' => false, 'error' => 'Game session too short'];
        }

        if ($duration > self::MAX_GAME_SECONDS) {
            return ['ok' => false, 'error' => 'Game session expired'];
        }

        $maxAllowedScore = ($duration * self::MAX_SCORE_PER_SECOND) + self::SCORE_BASE_BUFFER;

        if ($score > $maxAllowedScore) {
            return ['ok' => false, 'error' => 'Score too high for session duration'];
        }

        return ['ok' => true];
    }

    public function markUsed(GameSession $session): void
    {
        $session->markUsed();
        $this->em->flush();
    }
}