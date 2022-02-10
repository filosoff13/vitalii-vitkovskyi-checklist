<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Activity\EditTaskActivity;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TaskActivityService
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function createNoteEditActivity(Task $task)
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if (!$user instanceof User) {
            throw new HttpException(400, 'User not exists in request');
        }

        $activity = new EditTaskActivity($user, $task);

        $this->em->persist($activity);
        $this->em->flush();
    }
}
