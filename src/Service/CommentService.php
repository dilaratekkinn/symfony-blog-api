<?php

namespace App\Service;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Security;

class BlogService
{
    private $em;
    private $blogPostRepository;
    private $security;
    private $categoryRepository;
    private $tagRepository;

    /**
     * @param EntityManagerInterface $em
     * @param BlogPostRepository $blogPostRepository
     * @param Security $security
     * @param CategoryRepository $categoryRepository
     * @param TagRepository $tagRepository
     */

    public function __construct(EntityManagerInterface $em, BlogPostRepository $blogPostRepository, Security $security, CategoryRepository $categoryRepository, TagRepository $tagRepository)
    {
        $this->em = $em;
        $this->blogPostRepository = $blogPostRepository;
        $this->security = $security;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;

    }

    public function index(array $parameters)
    {
        $defaults = [
            'pageNumber' => 1,
            'rowsPerPage' => '',
            'searchText' => '',
            'orderBy' => 'id',
            'order' => 'desc'
        ];


        $parameters = array_merge($defaults, $parameters);
        $repo = $this->em->getRepository(BlogPost::class);
        $blogs = $repo->createQueryBuilder('b');

        $blogs->setMaxResults($parameters['rowsPerPage'])->setFirstResult($parameters['pageNumber'] - 1)->orderBy('b.id', 'desc');
        if (isset($parameters['user'])) {
            $blogs
                ->join('b.User', 'u')
                ->andWhere('u.id = :user')
                ->setParameter('user', $parameters['user']);
        }

        if (isset($parameters['category'])) {
            $blogs
                ->join('b.categories', 'c')
                ->andWhere('c.id = :category')
                ->setParameter('category', $parameters['category']);

        }

        if (isset($parameters['tag'])) {
            $blogs
                ->join('b.tag', 't')
                ->andWhere('t.name = :tag')
                ->setParameter('tag', $parameters['tag']);

        }

        return $blogs
            ->getQuery()
            ->getResult();

    }


    /**
     * @throws \Exception
     */
    public function addBlogPost(array $parameters): BlogPost
    {
        $blog = $this->blogPostRepository->findOneBy(['title' => $parameters['title']]);
        $categories = $this->categoryRepository->getCategoriesById($parameters['categories']);
        $tags = $this->tagRepository->getTagsByName($parameters['tags']);

        if ($blog) {
            throw new \Exception('BLog Already Exists,Please Create New One');
        }

        $blog = new BlogPost();
        $blog->setTitle($parameters['title']);
        $blog->setContent($parameters['content']);
        $blog->setStatus(1);

        foreach ($categories as $category) {
            $blog->addCategory($category);
        }

        foreach ($tags as $tag) {
            $blog->addTag($tag);
        }

        $blog->setUser($this->security->getUser());
        $this->em->persist($blog);
        $this->em->flush();
        return $blog;
    }


    /**
     * @throws \Exception
     */
    public function show($id): BlogPost
    {

        $blog = $this->blogPostRepository->findOneBy(['id' => $id]);
        if (!$blog) {
            throw new \Exception('There is any blog with this id');

        }
        return $blog;
    }

    /**
     * @throws \Exception
     */
    public function delete($id): bool
    {
        $credentials = $this->credential($id);
        $blog = $this->blogPostRepository->findOneBy($credentials);
        if ($blog === null) {
            throw new \Exception('There is any post with this user');
        }

        $this->em->remove($blog);
        $this->em->flush();
        return true;
    }

    /**
     * @throws \Exception
     */
    public function update(array $parameters, $id): BlogPost
    {

        $credentials = $this->credential($id);

        $blog = $this->blogPostRepository->findOneBy($credentials);
        if ($blog === null) {
            throw new \Exception('There is any post with this user');
        }

        $checkAnother = $this->blogPostRepository->check($id, $parameters['title']);

        if ($checkAnother != null) {
            throw new \Exception('Title is Already Given');
        }

        $blog->setTitle($parameters['title']);
        $blog->setContent($parameters['content']);
        $blog->setStatus(1);
        $this->em->persist($blog);
        $this->em->flush();
        return $blog;
    }

    private function credential($id): array
    {

        $credential = [
            'id' => $id
        ];
        $user = $this->security->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $credential['User'] = $user;
        }
        return $credential;
    }
}