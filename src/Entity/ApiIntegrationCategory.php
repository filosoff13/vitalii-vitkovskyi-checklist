<?php

namespace App\Entity;

use App\Repository\ApiIntegrationCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApiIntegrationCategoryRepository::class)
 */
class ApiIntegrationCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ApiIntegration::class)
     */
    private $apiIntegration;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="integer")
     */
    private $externalId;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
