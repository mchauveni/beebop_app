<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $doAt = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Beehive $beehive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDoAt(): ?\DateTimeImmutable
    {
        return $this->doAt;
    }

    public function setDoAt(\DateTimeImmutable $doAt): self
    {
        $this->doAt = $doAt;

        return $this;
    }

    public function getBeehive(): ?Beehive
    {
        return $this->beehive;
    }

    public function setBeehive(?Beehive $beehive): self
    {
        $this->beehive = $beehive;

        return $this;
    }
}