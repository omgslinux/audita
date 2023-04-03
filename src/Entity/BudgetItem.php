<?php

namespace App\Entity;

use App\Repository\BudgetItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: BudgetItemRepository::class)]
#[UniqueConstraint(name: "budgetItem", columns: ["year_id", "center_id", "programm_id", "subconcept_id"])]
class BudgetItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'budgetItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BudgetYear $year = null;

    #[ORM\ManyToOne(inversedBy: 'budgetItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ManagementCenter $center = null;

    #[ORM\ManyToOne(inversedBy: 'budgetItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Programm $programm = null;

    #[ORM\ManyToOne(inversedBy: 'budgetItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subconcept $subconcept = null;

    #[ORM\Column]
    private ?float $InitialCredit = null;

    #[ORM\Column]
    private ?float $CurrentCredit = null;

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

    public function getCenter(): ?ManagementCenter
    {
        return $this->center;
    }

    public function setCenter(?ManagementCenter $center): self
    {
        $this->center = $center;

        return $this;
    }

    public function getProgramm(): ?Programm
    {
        return $this->programm;
    }

    public function setProgramm(?Programm $programm): self
    {
        $this->programm = $programm;

        return $this;
    }

    public function getSubconcept(): ?Subconcept
    {
        return $this->subconcept;
    }

    public function setSubconcept(?Subconcept $subconcept): self
    {
        $this->subconcept = $subconcept;

        return $this;
    }

    public function getInitialCredit(): ?float
    {
        return $this->InitialCredit;
    }

    public function setInitialCredit(float $InitialCredit): self
    {
        $this->InitialCredit = $InitialCredit;

        return $this;
    }

    public function getCurrentCredit(): ?float
    {
        return $this->CurrentCredit;
    }

    public function setCurrentCredit(float $CurrentCredit): self
    {
        $this->CurrentCredit = $CurrentCredit;

        return $this;
    }
}
