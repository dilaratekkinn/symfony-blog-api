<?php

namespace App\Controller\Admin;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Request\CreateCategoryRequest;
use App\Request\UpdateCategoryRequest;
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
 * @Route(path="api/admin/category")
 */
class AdminCategoryController extends AbstractController
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
     * @Route("/", name="categories")
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
     * @Route("/create", name="create_category", methods={"POST"})
     * @param CreateCategoryRequest $request
     * @return JsonResponse
     */
    public function create(CreateCategoryRequest $request): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->categoryService->addCategory($request->toArray())
            )->setMessages(['Category Created Successfully'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }


    /**
     * @Route("/delete/{id}", name="delete_category", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        try {
            $this->categoryService->delete($id);
            return $this->successResponse->setMessages(
                'Category Deleted Successfully'
            )->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/show/{id}", name="admin_show_category", methods={"GET"})
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


    /**
     * @Route("/update/{id}", name="update_category", methods={"PUT"})
     */
    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->categoryService->update($request->toArray(), $id)
            )->setMessages(['Category Updated'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

}
