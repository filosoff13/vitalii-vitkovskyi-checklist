<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Activity\Activity;
use App\Entity\Activity\VisitActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @param int $offset
     * @param int $itemsPerPage
     *
     * @return VisitActivity[]
     */
    public function getVisitActivityData(int $offset = 0, int $itemsPerPage = 20): array
    {
        return $this->createQueryBuilder('activity')
            ->orderBy('activity.createdAt', 'DESC')
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset)
            ->where('activity INSTANCE OF App\Entity\Activity\VisitActivity')
            ->getQuery()
            ->getResult();
    }

    public function getTaskActivityData(UserInterface $user): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare('
             SELECT activity.created_at AS created_at,
             task.title AS task_name

             FROM activity

             JOIN task ON task.id = activity.task_id
            
             WHERE type = :type
             AND activity.user_id = :user 
             ORDER BY created_at DESC
         ');

        $result = $stmt->executeQuery([
           'type' => 'edit_task',
           'user' => $user->getId()
        ]);

        return $result->fetchAllAssociative();
    }
}
