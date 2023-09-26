<?php

namespace App\Controller\User;

use App\ApiResponse\FailResponse;
use App\ApiResponse\SuccessResponse;
use App\Request\CreateCommentRequest;
use App\Service\CommentService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 * @package App\Controller
 * @Route(path="api/user/comment")
 */
class CommentController extends AbstractController
{

    private $failResponse;
    private $successResponse;
    private $commentService;
    private $serializer;

    public function __construct(
        FailResponse    $failResponse,
        SuccessResponse $successResponse,
        CommentService  $commentService,
        Serializer      $serializer
    )
    {
        $this->commentService = $commentService;
        $this->failResponse = $failResponse;
        $this->successResponse = $successResponse;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/comment", name="app_comment")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CommentController.php',
        ]);
    }

    /**
     * @Route("/create/{blog_id}", name="user_comment_create", methods={"POST"})
     * @param CreateCommentRequest $request
     * @return JsonResponse
     */

    public function create(CreateCommentRequest $request, $blog_id): JsonResponse
    {
        try {
            return $this->successResponse->setData(
                $this->commentService->addComment($request->toArray(), $blog_id)
            )->setMessages(['Comment Created Successfully'])
                ->send();
        } catch (\Exception $e) {
            return $this->failResponse->setMessages([
                'main' => $e->getMessage(),
            ])->send();
        }
    }

    /**
     * @Route("/{id}", name="app_blog", methods={"GET"})
     */
}
