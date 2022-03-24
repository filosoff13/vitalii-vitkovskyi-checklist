<?php

declare(strict_types=1);

namespace App\Entity;

use App\Model\Ownable;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category implements Ownable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups("API")
     */
    private ?int $id = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Category title should not be blank")
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 30,
     *      minMessage = "Your Category title must be at least {{ limit }} characters long",
     *      maxMessage = "Your Category title cannot be longer than {{ limit }} characters"
     * )
     *
     * @Groups("API")
     */
    private string $title;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="category", orphanRemoval=true)
     */
    private Collection $tasks;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private UserInterface $user;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Category
     */
    public function setTitle(string $title): Category
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setCategory($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getCategory() === $this) {
                $task->setCategory(null);
            }
        }

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
