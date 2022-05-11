<?php

namespace App\Repository;

use App\Entity\ApiIntegration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ErrorException;

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

    public function findOneOrNullBy(array $criteria): ?ApiIntegration {
        try {
            return $this->findOneBy($criteria);
        } catch (ErrorException $e) {
            return null;
        }
    }
}
