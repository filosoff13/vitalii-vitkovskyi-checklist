<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ApiIntegrationTaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApiIntegrationTaskRepository::class)
 */
class ApiIntegrationTask
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ApiIntegration::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ApiIntegration $apiIntegration;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Task $task;

    /**
     * @ORM\Column(type="integer")
     */
    private int $externalId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiIntegration(): ?ApiIntegration
    {
        return $this->apiIntegration;
    }

    public function setApiIntegration(?ApiIntegration $apiIntegration): self
    {
        $this->apiIntegration = $apiIntegration;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }
}
