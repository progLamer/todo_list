<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository", repositoryClass=TaskRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=false, hardDelete=false)
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}},
 *     itemOperations={
 *         "get",
 *         "delete",
 *         "patch"={"denormalization_context"={"groups"={"patch"}}}
 *     },
 * )
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups("read")
     */
    private ?int $id = null;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *     max=1000
     * )
     * @ORM\Column(type="text")
     *
     * @Groups({"read","write"})
     */
    private string $text;

    /**
     * @ORM\ManyToOne(targetEntity=TaskStatus::class, inversedBy="tasks", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"read","patch"})
     *
     * @ApiFilter(SearchFilter::class, properties={"status.name"="exact"})
     */
    private ?TaskStatus $status = null;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetimetz")
     *
     * @Groups("read")
     */
    private DateTimeInterface $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetimetz")
     *
     * @Groups("read")
     */
    private DateTimeInterface $updated;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private DateTimeInterface $deleted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): ?TaskStatus
    {
        return $this->status;
    }

    public function setStatus(?TaskStatus $status): self
    {
        $this->status = $status;

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

    public function getUpdated(): ?DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }
}
