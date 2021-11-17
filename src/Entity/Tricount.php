<?php

namespace App\Entity;

use App\Repository\TricountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TricountRepository::class)
 */
class Tricount
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
     * @ORM\Column(type="string", length=3)
     */
    private $device;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Useraccount::class, inversedBy="tricount")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity=Useraccount::class, inversedBy="supp")
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity=Expense::class, mappedBy="tricountId")
     */
    private $tricountId;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->tricountId = new ArrayCollection();
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

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedBy(): ?Useraccount
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Useraccount $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection|Useraccount[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Useraccount $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Useraccount $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return Collection|Expense[]
     */
    public function getTricountId(): Collection
    {
        return $this->tricountId;
    }

    public function addTricountId(Expense $tricountId): self
    {
        if (!$this->tricountId->contains($tricountId)) {
            $this->tricountId[] = $tricountId;
            $tricountId->setTricount($this);
        }

        return $this;
    }

    public function removeTricountId(Expense $tricountId): self
    {
        if ($this->tricountId->removeElement($tricountId)) {
            // set the owning side to null (unless already changed)
            if ($tricountId->getTricount() === $this) {
                $tricountId->setTricount(null);
            }
        }

        return $this;
    }
}
