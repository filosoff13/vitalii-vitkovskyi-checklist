<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Task;
use App\Service\TaskActivityService;
use Doctrine\ORM\EntityManagerInterface;

class EditTaskActivityListener
{
    private TaskActivityService $noteActivityService;
    private EntityManagerInterface $em;

    public function __construct(TaskActivityService $noteActivityService, EntityManagerInterface $em)
    {
        $this->noteActivityService = $noteActivityService;
        $this->em = $em;
    }

    public function postUpdate(Task $task): void
    {
        $uow = $this->em->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($task);

        $this->noteActivityService->createNoteEditActivity($task, $changes);
    }
}
