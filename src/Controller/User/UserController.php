<?php

namespace App\Controller\User;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Request\UpdateUserRequest;
use App\Service\UserService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


/**
 * Class AdminUserController
 * @package App\Controller
 * @Route(path="api/user")
 */
class UserController extends AbstractController
{
    private $failResponse;
    private $successResponse;
    private $userService;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    private $security;

    public function __construct(
        UserService         $userService,
        FailResponse        $failResponse,
        SuccessResponse     $successResponse,
        SerializerInterface $serializer,
        Security            $security
    )
    {
        $this->userService = $userService;
        $this->successResponse = $successResponse;
        $this->failResponse = $failResponse;
        $this->serializer = $serializer;
        $this->security = $security;
    }

    /**
     * @Route("/", name="u_users", methods={"GET"})
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $users = $this->userService->index($request->toArray());
            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($users, 'json', SerializationContext::create()->setGroups('user'))),
                ]
            )->setMessages(['Users Listed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/show/{id}", name="u_show_user", methods={"GET"})
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->userService->show($id);
            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($user, 'json', SerializationContext::create()->setGroups('user'))),
                ]
            )->setMessages([$user->getName() . '-Showed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/update/{id}", name="u_update_user", methods={"PUT"})
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->userService->update($request->toArray(), $this->security->getUser()->getId())
            )->setMessages(['User Updated'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }


}
