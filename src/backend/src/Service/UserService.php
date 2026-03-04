<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    private UserRepository $repo;
    private PasswordService $passwordService;

    public function __construct(UserRepository $repo, PasswordService $passwordService)
    {
        $this->repo = $repo;
        $this->passwordService = $passwordService;
    }

    /** @return array<string,string> */
    public function register(string $username, string $password): array
    {
        if ($this->repo->findOneBy(['username' => $username])) {
            return ['error' => 'User exists'];
        }

        $encrypted = $this->passwordService->encrypt($password);
        $user = new User($username, $encrypted);
        $this->repo->getEntityManager()->persist($user);
        $this->repo->getEntityManager()->flush();

        return ['status' => 'ok'];
    }

    public function getPassword(string $username): ?string
    {
        $user = $this->repo->findOneBy(['username' => $username]);
        if (!$user) return null;
        return $this->passwordService->decrypt($user->getPassword());
    }
}
