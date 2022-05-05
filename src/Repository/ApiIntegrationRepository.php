<?php

namespace App\Repository;

use App\Entity\ApiIntegration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApiIntegration|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiIntegration|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiIntegration[]    findAll()
 * @method ApiIntegration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiIntegrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiIntegration::class);
    }

    // /**
    //  * @return ApiIntegration[] Returns an array of ApiIntegration objects
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
    public function findOneBySomeField($value): ?ApiIntegration
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
