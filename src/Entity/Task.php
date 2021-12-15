<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="Task title should not be blank")
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 30,
     *      minMessage = "Your task title must be at least {{ limit }} characters long",
     *      maxMessage = "Your task title cannot be longer than {{ limit }} characters"
     * )
     */
    private string $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Task text should not be blank")
     *
     * @Assert\Length(
     *      min = 30,
     *      max = 254,
     *      minMessage = "Your task text must be at least {{ limit }} characters long",
     *      maxMessage = "Your task text cannot be longer than {{ limit }} characters"
     * )
     */
    private string $text;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $done;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank(message="Category cannot be empty")
     */
    private Category $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private UserInterface $user;

    public function __construct(string $title, string $text, Category $category, UserInterface $user, bool $done = false)
    {
        $this->title = $title;
        $this->text = $text;
        $this->category = $category;
        $this->user = $user;
        $this->done = $done;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Task
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): Task
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * @param bool $done
     * @return Task
     */
    public function setDone(bool $done): Task
    {
        $this->done = $done;
        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
}
