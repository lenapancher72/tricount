<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExpenseRepository::class)
 */
class Expense
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=Tricount::class, inversedBy="tricountId")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tricount;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userPaid")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userPaid;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="userRefund")
     */
    private $userRefund;

    public function __construct()
    {
        $this->userRefund = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTricount(): ?Tricount
    {
        return $this->tricount;
    }

    public function setTricount(?Tricount $tricount): self
    {
        $this->tricount = $tricount;

        return $this;
    }

    public function getUserPaid(): ?User
    {
        return $this->userPaid;
    }

    public function setUserPaid(?User $userPaid): self
    {
        $this->userPaid = $userPaid;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserRefund(): Collection
    {
        return $this->userRefund;
    }

    public function addUserRefund(User $userRefund): self
    {
        if (!$this->userRefund->contains($userRefund)) {
            $this->userRefund[] = $userRefund;
        }

        return $this;
    }

    public function removeUserRefund(User $userRefund): self
    {
        $this->userRefund->removeElement($userRefund);

        return $this;
    }
}
