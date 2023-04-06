<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Expression(
        "this.getDate() <= this.getCurrentDate()",
        message: 'La date ne peut pas être dans le futur.'
    )]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?float $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

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
