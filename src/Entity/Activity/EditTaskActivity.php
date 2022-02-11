<?php

declare(strict_types=1);

namespace App\Entity\Activity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class EditTaskActivity extends Activity
{
    /**
     * @ORM\ManyToOne(targetEntity=Task::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Task $task;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private array $changes = [];

    public function __construct(User $user, Task $task, array $changes) {
        parent::__construct($user);
        $this->task = $task;
        $this->changes = $changes;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setNote(Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getChanges(): ?array
    {
        return $this->changes;
    }

    public function setChanges(?array $changes): self
    {
        $this->changes = $changes;

        return $this;
    }
}
