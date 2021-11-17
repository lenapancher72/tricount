<?php

namespace App\Entity;

use App\Repository\UseraccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UseraccountRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Useraccount implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Tricount::class, mappedBy="createdBy", orphanRemoval=true)
     */
    private $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity=Tricount::class, mappedBy="participants")
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity=Expense::class, mappedBy="userPaid")
     */
    private $userPaid;

    /**
     * @ORM\ManyToMany(targetEntity=Expense::class, mappedBy="userRefund")
     */
    private $userRefund;

    public function __construct()
    {
        $this->createdBy = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->userPaid = new ArrayCollection();
        $this->userRefund = new ArrayCollection();
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

    /**
     * @return Collection|Tricount[]
     */
    public function getCreatedBy(): Collection
    {
        return $this->createdBy;
    }

    public function addTricount(Tricount $tricount): self
    {
        if (!$this->createdBy->contains($tricount)) {
            $this->createdBy[] = $tricount;
            $tricount->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTricount(Tricount $tricount): self
    {
        if ($this->createdBy->removeElement($tricount)) {
            // set the owning side to null (unless already changed)
            if ($tricount->getCreatedBy() === $this) {
                $tricount->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tricount[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addSupp(Tricount $supp): self
    {
        if (!$this->participants->contains($supp)) {
            $this->participants[] = $supp;
            $supp->addParticipant($this);
        }

        return $this;
    }

    public function removeSupp(Tricount $supp): self
    {
        if ($this->participants->removeElement($supp)) {
            $supp->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Expense[]
     */
    public function getUserPaid(): Collection
    {
        return $this->userPaid;
    }

    public function addUserPaid(Expense $userPaid): self
    {
        if (!$this->userPaid->contains($userPaid)) {
            $this->userPaid[] = $userPaid;
            $userPaid->setUserPaid($this);
        }

        return $this;
    }

    public function removeUserPaid(Expense $userPaid): self
    {
        if ($this->userPaid->removeElement($userPaid)) {
            // set the owning side to null (unless already changed)
            if ($userPaid->getUserPaid() === $this) {
                $userPaid->setUserPaid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Expense[]
     */
    public function getUserRefund(): Collection
    {
        return $this->userRefund;
    }

    public function addUserRefund(Expense $userRefund): self
    {
        if (!$this->userRefund->contains($userRefund)) {
            $this->userRefund[] = $userRefund;
            $userRefund->addUserRefund($this);
        }

        return $this;
    }

    public function removeUserRefund(Expense $userRefund): self
    {
        if ($this->userRefund->removeElement($userRefund)) {
            $userRefund->removeUserRefund($this);
        }

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
