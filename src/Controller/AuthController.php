<?php

namespace App\Controller;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private $failResponse;
    private $successResponse;
    private $userService;

    public function __construct(
        UserService     $userService,
        FailResponse    $failResponse,
        SuccessResponse $successResponse
    )
    {
        $this->userService = $userService;
        $this->successResponse = $successResponse;
        $this->failResponse = $failResponse;

    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {

        try {
            return $this->successResponse->setData(
                $this->userService->register($request->toArray())
            )->setMessages('User Created Successfully')
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(LoginRequest $request, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        try {
            $user = $this->userService->login($request->toArray());
            return $this->successResponse->setData([
                'token' => $JWTManager->create($user)
            ])->setMessages([
                "Welcome ".$user->getName()
            ])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }
}
