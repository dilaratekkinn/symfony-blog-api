<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function add(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function check($id, $name)
    {
        $qb = $this->createQueryBuilder('c');
        return $qb
            ->where('c.id != :id')
            ->andWhere('c.name = :name')
            ->setParameters([
                'id' => $id,
                'name' => $name,
            ])
            ->getQuery()
            ->getResult();

    }

    /**
     * @param array $ids
     * @return array<int, Category>
     */
    public function getCategoriesById(array $ids): array
    {
        $qb = $this->createQueryBuilder('c');
        return $qb
            ->where("c.id IN(:ids)")
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

    }

    /**
     * @param $id
     * @return Category|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCategoryWithPosts($id): ?Category
    {
        $qb = $this->createQueryBuilder('c');
        return $qb
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->join('c.blogPosts', 'b')
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
