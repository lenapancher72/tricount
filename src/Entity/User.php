<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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
}
