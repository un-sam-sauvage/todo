<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'categories')]
    private Collection $Task;

    #[ORM\Column]
    private ?bool $isPublic = null;

    public function __construct()
    {
        $this->Task = new ArrayCollection();
        $this->isPublic = false;
    }

    public function __toString()
	{
		return $this->name;
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTask(): Collection
    {
        return $this->Task;
    }

    public function addTask(Task $task): static
    {
        if (!$this->Task->contains($task)) {
            $this->Task->add($task);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        $this->Task->removeElement($task);

        return $this;
    }

    public function isIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }
}
