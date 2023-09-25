<?php

namespace App\Controller\User;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Service\CategoryService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route(path="api/user/category")
 */
class CategoryController extends AbstractController
{
    private $failResponse;
    private $successResponse;
    private $categoryService;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SuccessResponse     $successResponse,
        FailResponse        $failResponse,
        CategoryService     $categoryService,
        SerializerInterface $serializer
    )
    {
        $this->categoryService = $categoryService;
        $this->successResponse = $successResponse;
        $this->failResponse = $failResponse;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="u_categories")
     */
    public function index(Request $request): JsonResponse
    {

        try {
            $categories = $this->categoryService->index($request->toArray());
            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($categories, 'json', SerializationContext::create()->setGroups('category'))),
                ]
            )->setMessages(
                ['Categories Listed Successfully']
            )->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }

    }

    /**
     * @Route("/show/{id}", name="show_category", methods={"GET"})
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $category = $this->categoryService->show($id);
            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($category, 'json', SerializationContext::create()->setGroups('category'))),
                ]
            )->setMessages(['Categories Listed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

}
