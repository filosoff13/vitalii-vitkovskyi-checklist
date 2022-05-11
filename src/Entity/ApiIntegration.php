<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ApiIntegrationsEnum;
use App\Repository\ApiIntegrationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApiIntegrationRepository::class)
 */
class ApiIntegration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private ApiIntegrationsEnum $type;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private User $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $enabled = true;

    /**
     * @ORM\Column(type="json")
     */
    private array $config = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?ApiIntegrationsEnum
    {
        return $this->type;
    }

    public function setType(ApiIntegrationsEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }
}
