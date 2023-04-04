<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BeekeeperRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: BeekeeperRepository::class)]
#[UniqueEntity(
    fields: ['mail', 'login'],
    message: '"{{ value }}" est déjà utilisé'
)]
class Beekeeper implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Utiliser au moins {{ limit }} caractères",
        maxMessage: "Ne pas dépasser {{ limit }} caractères"
    )]
    private ?string $lastName = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Utiliser au moins {{ limit }} caractères",
        maxMessage: "Ne pas dépasser {{ limit }} caractères"
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Utiliser au moins {{ limit }} caractères",
        maxMessage: "Ne pas dépasser {{ limit }} caractères"
    )]
    private ?string $login = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
        max: 48,
        minMessage: "Utiliser au moins {{ limit }} caractères",
        maxMessage: "Ne pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$@!%&*?])[A-Za-z\d#$@!%&*?]{8,48}$/',
        htmlPattern: '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$@!%&*?])[A-Za-z\d#$@!%&*?]{8,48}$',
        message: 'Votre mot de passe doit contenir au minimum 1 caractère spécial, 1 chiffre, 1 majuscule et minuscule',
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(
        message: 'Le mail "{{ value }}" n\'est pas valide.',
    )]
    private ?string $mail = null;

    #[ORM\Column]
    private ?bool $verified = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'beekeeper', targetEntity: Apiary::class)]
    private Collection $apiaries;

    #[ORM\Column]
    private array $roles = [];

    public function __construct()
    {

        $this->setCreatedAt(new \DateTimeImmutable());
        $this->apiaries = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstName;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Apiary>
     */
    public function getApiaries(): Collection
    {
        return $this->apiaries;
    }

    public function addApiary(Apiary $apiary): self
    {
        if (!$this->apiaries->contains($apiary)) {
            $this->apiaries->add($apiary);
            $apiary->setBeekeeper($this);
        }

        return $this;
    }

    public function removeApiary(Apiary $apiary): self
    {
        if ($this->apiaries->removeElement($apiary)) {
            // set the owning side to null (unless already changed)
            if ($apiary->getBeekeeper() === $this) {
                $apiary->setBeekeeper(null);
            }
        }

        return $this;
    }
}
