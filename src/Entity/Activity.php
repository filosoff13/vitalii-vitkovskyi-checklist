<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActivityRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 */
class Activity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private string $method;

    /**
     * @ORM\Column(type="text")
     */
    private string $url;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?User $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private ?string $ip;

    /**
     * @ORM\Column(type="integer")
     */
    private int $statusCode;

    public function __construct(
        string $method,
        string $url,
        DateTime $createdAt,
        int $statusCode,
        string $ip = null,
        User $user = null
    ) {
        $this->method = $method;
        $this->url = $url;
        $this->user = $user;
        $this->createdAt = $createdAt;
        $this->ip = $ip;
        $this->statusCode = $statusCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
