<?php

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
    private $id;

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
}
