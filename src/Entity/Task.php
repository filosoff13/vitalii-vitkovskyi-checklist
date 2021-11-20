<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

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
    private ?int $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private string $title;

    /**
     * @var string
     * @ORM\Column(type="text")
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
     */
    private Category $category;

    /**
     * @param string $title
     * @param string $text
     * @param Category $category
     */
    public function __construct(string $title, string $text, Category $category, bool $done = false)
    {
        $this->title = $title;
        $this->text = $text;
        $this->category = $category;
        $this->done = $done;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(string $title): Task
    {
        $this->title = $title;
        return $this;
    }

    public function setText(string $text): Task
    {
        $this->text = $text;
        return $this;
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
}
