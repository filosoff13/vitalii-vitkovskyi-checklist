<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Task;
use App\Service\TaskActivityService;

class EditTaskActivityListener
{
    private TaskActivityService $noteActivityService;

    public function __construct(TaskActivityService $noteActivityService)
    {
        $this->noteActivityService = $noteActivityService;
    }

    public function postUpdate(Task $task): void
    {
        $this->noteActivityService->createNoteEditActivity($task);
    }
}
