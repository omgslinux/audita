<?php

namespace App\Entity;

use App\Repository\ManagementCenterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: ManagementCenterRepository::class)]
#[UniqueConstraint(name: "yearcode", columns: ["year_id", "code"])]
class ManagementCenter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'managementCenters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BudgetYear $year = null;

    #[ORM\Column(length: 16)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'center', targetEntity: BudgetItem::class)]
    private Collection $budgetItems;

    public function __construct()
    {
        $this->budgetItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?BudgetYear
    {
        return $this->year;
    }

    public function setYear(?BudgetYear $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, BudgetItem>
     */
    public function getBudgetItems(): Collection
    {
        return $this->budgetItems;
    }

    public function addBudgetItem(BudgetItem $budgetItem): self
    {
        if (!$this->budgetItems->contains($budgetItem)) {
            $this->budgetItems->add($budgetItem);
            $budgetItem->setCenter($this);
        }

        return $this;
    }

    public function removeBudgetItem(BudgetItem $budgetItem): self
    {
        if ($this->budgetItems->removeElement($budgetItem)) {
            // set the owning side to null (unless already changed)
            if ($budgetItem->getCenter() === $this) {
                $budgetItem->setCenter(null);
            }
        }

        return $this;
    }
}
