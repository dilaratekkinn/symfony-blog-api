<?php

namespace App\Controller\Admin;

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


/**
 * Class AdminUserController
 * @package App\Controller
 * @Route(path="api/admin/user")
 */
class AdminUserController extends AbstractController
{
    private $failResponse;
    private $successResponse;
    private $userService;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        UserService         $userService,
        FailResponse        $failResponse,
        SuccessResponse     $successResponse,
        SerializerInterface $serializer
    )
    {
        $this->userService = $userService;
        $this->successResponse = $successResponse;
        $this->failResponse = $failResponse;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="admin_users", methods={"GET"})
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
     * @Route("/show/{id}", name="admin_show_user", methods={"GET"})
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->userService->show($id);

            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($user, 'json', SerializationContext::create()->setGroups(['user','user_role']))),
                ]
            )->setMessages([$user->getName() . '-Showed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/update/{id}", name="admin_update_user", methods={"PUT"})
     */
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->userService->update($request->toArray(), $id)
            )->setMessages(['User Updated'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/delete/{id}", name="admin_delete_user", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        try {
            $this->userService->delete($id);
            return $this->successResponse->setMessages(['User Deleted Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }


}
