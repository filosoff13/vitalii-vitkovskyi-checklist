<?php

namespace App\Repository;

use App\Entity\ApiIntegrationTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApiIntegrationTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiIntegrationTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiIntegrationTask[]    findAll()
 * @method ApiIntegrationTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiIntegrationTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiIntegrationTask::class);
    }

    // /**
    //  * @return ApiIntegrationTask[] Returns an array of ApiIntegrationTask objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApiIntegrationTask
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
