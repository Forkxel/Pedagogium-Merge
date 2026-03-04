<?php
namespace App\Service;

use App\Entity\Score;
use App\Repository\ScoreRepository;
use App\Repository\UserRepository;

class ScoreService
{
    private ScoreRepository $scoreRepo;
    private UserRepository $userRepo;

    public function __construct(ScoreRepository $scoreRepo, UserRepository $userRepo)
    {
        $this->scoreRepo = $scoreRepo;
        $this->userRepo = $userRepo;
    }

    /** @return array<string,string> */
    public function saveHighscore(string $username, int $score): array
    {
        $user = $this->userRepo->findOneBy(['username' => $username]);
        if (!$user) return ['error' => 'User not found'];

        $existingScore = $this->scoreRepo->findOneBy(['user' => $user]);
        if ($existingScore) {
            if ($score > $existingScore->getValue()) {
                $existingScore->setValue($score);
                $this->scoreRepo->getEntityManager()->flush();
            }
        } else {
            $newScore = new Score($user, $score);
            $this->scoreRepo->getEntityManager()->persist($newScore);
            $this->scoreRepo->getEntityManager()->flush();
        }

        return ['status' => 'saved'];
    }

    /** @return Score[] */
    public function getTop5(): array
    {
        return $this->scoreRepo->findBy([], ['value' => 'DESC'], 5);
    }
}
