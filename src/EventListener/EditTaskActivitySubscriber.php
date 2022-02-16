<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Task;
use App\Service\TaskActivityService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class EditTaskActivitySubscriber implements EventSubscriberInterface
{
    private TaskActivityService $noteActivityService;

    public function __construct(TaskActivityService $noteActivityService)
    {
        $this->noteActivityService = $noteActivityService;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Task) {
            return;
        }

        $this->noteActivityService->createNoteEditActivity($entity);
    }
}
