<?php
namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private UserService $service) {}

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = isset($data['username']) ? (string)$data['username'] : '';
        $password = isset($data['password']) ? (string)$data['password'] : '';

        if (!$username || !$password) {
            return new JsonResponse(['error' => 'Missing data'], 400);
        }

        return new JsonResponse($this->service->register($username, $password));
    }
}
