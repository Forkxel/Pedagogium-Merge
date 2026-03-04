<?php
namespace App\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController
{
    public function __construct(private UserService $userService) {}

    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = isset($data['username']) ? (string)$data['username'] : '';
        $password = isset($data['password']) ? (string)$data['password'] : '';

        if ($username === '' || $password === '') {
            return new JsonResponse(['error' => 'Invalid input'], 400);
        }

        $result = $this->userService->register($username, $password);
        return new JsonResponse($result);
    }
}
