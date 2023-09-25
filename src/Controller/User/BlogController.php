<?php

namespace App\Controller\User;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Request\CreateBlogRequest;
use App\Request\UpdateBlogRequest;
use App\Service\BlogService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class BlogController
 * @package App\Controller
 * @Route(path="api/user/blog")
 */
class BlogController extends AbstractController
{

    private $failResponse;
    private $successResponse;
    private $blogService;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        BlogService         $blogService,
        FailResponse        $failResponse,
        SuccessResponse     $successResponse,
        SerializerInterface $serializer
    )
    {
        $this->blogService = $blogService;
        $this->successResponse = $successResponse;
        $this->failResponse = $failResponse;
        $this->serializer = $serializer;


    }

    /**
     * @Route("/", name="app_blog_index")
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $blogs = $this->blogService->index(array_merge($request->toArray(), $request->query->all()));
            $groupItems = [
                'category', 'user', 'tag'
            ];
            $groups = [
                'blog'
            ];

            if($request->query->has('with')) {
                foreach (explode(',', $request->query->get('with')) as $data) {
                    if (in_array($data, $groupItems)) {
                        $groups[] = $data;
                    }
                }
            }

            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($blogs, 'json', SerializationContext::create()->setGroups($groups))),
                ]
            )->setMessages(['Blogs Listed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }

    }

    /**
     * @Route("/{id}", name="app_blog", methods={"GET"})
     */
    public function show(Request $request, $id): JsonResponse
    {

        try {
            $blog = $this->blogService->show($id);

            return $this->successResponse->setData([
                    json_decode($this->serializer->serialize($blog, 'json', SerializationContext::create()->setGroups(["blog", "blog_category", "category"]))),
                ]
            )->setMessages([$blog->getTitle() . '-Showed Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }

    }

    /**
     * @Route("/create", name="user_blog_create", methods={"POST"})
     * @param CreateBlogRequest $request
     * @return JsonResponse
     */
    public function create(CreateBlogRequest $request): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->blogService->addBlogPost($request->toArray())
            )->setMessages(['Category Created Successfully'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/update/{id}", name="update_blog", methods={"PUT"})
     * @param UpdateBlogRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateBlogRequest $request,$id): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->blogService->update($request->toArray(),$id)
            )->setMessages(['Category Updated Successfully'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/delete/{id}", name="delete_blog", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        try {
            $this->blogService->delete($id);
            return $this->successResponse->setMessages(['Blog Deleted Successfully'])->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }


}
