<?php

namespace App\Entity;

use App\Repository\ApiaryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiaryRepository::class)]
class Apiary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $zipCode = null;

    #[ORM\Column(length: 255)]
    private ?string $localisation = null;

    #[ORM\ManyToOne(inversedBy: 'apiaries')]
    private ?Beekeeper $beekeeper = null;

    #[ORM\OneToMany(mappedBy: 'apiary', targetEntity: Beehive::class)]
    private Collection $beehives;

    public function __construct()
    {
        $this->beehives = new ArrayCollection();
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getBeekeeper(): ?Beekeeper
    {
        return $this->beekeeper;
    }

    public function setBeekeeper(?Beekeeper $beekeeper): self
    {
        $this->beekeeper = $beekeeper;

        return $this;
    }

    /**
     * @return Collection<int, Beehive>
     */
    public function getBeehives(): Collection
    {
        return $this->beehives;
    }

    public function addBeehive(Beehive $beehive): self
    {
        if (!$this->beehives->contains($beehive)) {
            $this->beehives->add($beehive);
            $beehive->setApiary($this);
        }

        return $this;
    }

    public function removeBeehive(Beehive $beehive): self
    {
        if ($this->beehives->removeElement($beehive)) {
            // set the owning side to null (unless already changed)
            if ($beehive->getApiary() === $this) {
                $beehive->setApiary(null);
            }
        }

        return $this;
    }
}
