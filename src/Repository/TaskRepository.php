<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByUser(UserInterface $user): array
    {
        return $this->selectByUser($user)->getQuery()->getResult();
    }

    public function selectByCategoryAndUser(Category $category, UserInterface $user): QueryBuilder
    {
        return $this->selectByUser($user)
            ->andWhere('task.category = :category')
            ->setParameter(':category', $category);
    }

    public function selectByUser(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('task')
            ->select('task')
            ->join('task.users', 'user')
            ->where('user = :user')
            ->orderBy('task.id', 'DESC')
            ->setParameter(':user', $user);
    }
}
