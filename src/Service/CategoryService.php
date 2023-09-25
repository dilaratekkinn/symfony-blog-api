<?php

namespace App\Service;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CategoryService
{
    private $em;
    private $categoryRepository;
    private $security;


    /**
     * @param EntityManagerInterface $em
     * @param CategoryRepository $categoryRepository
     * @param Security $security
     */

    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepository, Security $security)
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->security = $security;
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
        $repo = $this->em->getRepository(Category::class);

        $categories = $repo->createQueryBuilder('c');
        return $categories->setMaxResults($parameters['rowsPerPage'])->setFirstResult($parameters['pageNumber'] - 1)
            ->orderBy('c.id', 'desc')
            ->getQuery()->getResult();
    }


    /**
     * @throws \Exception
     */
    public function addCategory(array $parameters): Category
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new \Exception('Erişim yok');
        }

        $category = $this->categoryRepository
            ->findOneBy(['name' => $parameters['name']]);
        if ($category) {
            throw new \Exception('There is Already A Category');
        }
        $category = new Category();
        $category->setName($parameters['name']);
        $category->setDescription($parameters['description']);

        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    /**
     * @throws \Exception
     */
    public function show($id): Category
    {
        $category = $this->categoryRepository->findOneBy(['id' => $id]);
        if ($category === null) {
            throw new \Exception('There is any BlogPost with this id ');
        }
        return $category;
    }

    /**
     * @throws \Exception
     */
    public function delete($id): bool
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new \Exception('Erişim yok');
        }
        $category = $this->categoryRepository
            ->findOneBy(['id' => $id,]);
        if ($category === null) {
            throw new \Exception('There is any category with this id');
        }
        $this->em->remove($category);
        $this->em->flush();
        return true;
    }

    /**
     * @throws \Exception
     */
    public function update(array $parameters, $id): Category
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new \Exception('Erişim yok');
        }
        $category = $this->categoryRepository
            ->findOneBy(['id' => $id]);
        if ($category === null) {
            throw new \Exception('There is any category with this id');
        }
        $checkAnother = $this->categoryRepository->check($id, $parameters['name']);
        if ($checkAnother != null) {
            throw new \Exception('Name is Already Given');
        }
        $category->setName($parameters['name']);
        $category->setDescription($parameters['description']);
        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }
}