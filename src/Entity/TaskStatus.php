<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskStatusRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaskStatusRepository::class)
 */
class TaskStatus
{
    const CREATED = 'Created';
    const DONE = 'Done';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups("read")
     */
    private ?int $id = null;

    /**
     * @Assert\IdenticalTo("Done")
     *
     * @ORM\Column(type="string", length=31, unique=true)
     *
     * @Groups({"read", "patch"})
     */
    private string $name;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetimetz")
     */
    private DateTimeInterface $created;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="status_id")
     */
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setStatus($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getStatus() === $this) {
                $task->setStatus(null);
            }
        }

        return $this;
    }
}
