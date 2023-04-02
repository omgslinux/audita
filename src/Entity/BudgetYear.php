<?php

namespace App\Entity;

use App\Repository\BudgetYearRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetYearRepository::class)]
class BudgetYear
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $year = null;

    #[ORM\OneToMany(mappedBy: 'year', targetEntity: ManagementCenter::class)]
    private Collection $managementCenters;

    #[ORM\OneToMany(mappedBy: 'year', targetEntity: Programm::class)]
    private Collection $programms;

    #[ORM\OneToMany(mappedBy: 'year', targetEntity: Subconcept::class)]
    private Collection $subconcepts;

    #[ORM\OneToMany(mappedBy: 'year', targetEntity: BudgetItem::class)]
    private Collection $budgetItems;

    public function __construct()
    {
        $this->managementCenters = new ArrayCollection();
        $this->programms = new ArrayCollection();
        $this->subconcepts = new ArrayCollection();
        $this->budgetItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Collection<int, ManagementCenter>
     */
    public function getManagementCenters(): Collection
    {
        return $this->managementCenters;
    }

    public function addManagementCenter(ManagementCenter $managementCenter): self
    {
        if (!$this->managementCenters->contains($managementCenter)) {
            $this->managementCenters->add($managementCenter);
            $managementCenter->setYear($this);
        }

        return $this;
    }

    public function removeManagementCenter(ManagementCenter $managementCenter): self
    {
        if ($this->managementCenters->removeElement($managementCenter)) {
            // set the owning side to null (unless already changed)
            if ($managementCenter->getYear() === $this) {
                $managementCenter->setYear(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Programm>
     */
    public function getProgramms(): Collection
    {
        return $this->programms;
    }

    public function addProgramm(Programm $programm): self
    {
        if (!$this->programms->contains($programm)) {
            $this->programms->add($programm);
            $programm->setYear($this);
        }

        return $this;
    }

    public function removeProgramm(Programm $programm): self
    {
        if ($this->programms->removeElement($programm)) {
            // set the owning side to null (unless already changed)
            if ($programm->getYear() === $this) {
                $programm->setYear(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subconcept>
     */
    public function getSubconcepts(): Collection
    {
        return $this->subconcepts;
    }

    public function addSubconcept(Subconcept $subconcept): self
    {
        if (!$this->subconcepts->contains($subconcept)) {
            $this->subconcepts->add($subconcept);
            $subconcept->setYear($this);
        }

        return $this;
    }

    public function removeSubconcept(Subconcept $subconcept): self
    {
        if ($this->subconcepts->removeElement($subconcept)) {
            // set the owning side to null (unless already changed)
            if ($subconcept->getYear() === $this) {
                $subconcept->setYear(null);
            }
        }

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
            $budgetItem->setYear($this);
        }

        return $this;
    }

    public function removeBudgetItem(BudgetItem $budgetItem): self
    {
        if ($this->budgetItems->removeElement($budgetItem)) {
            // set the owning side to null (unless already changed)
            if ($budgetItem->getYear() === $this) {
                $budgetItem->setYear(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getYear()->format('Y');
    }
}
