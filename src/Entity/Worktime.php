<?php

namespace App\Entity;

use App\Repository\WorktimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorktimeRepository::class)]
class Worktime
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $startDate = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $endDate = null;

  #[ORM\Column(type: Types::DATE_MUTABLE)]
  private ?\DateTimeInterface $startDay = null;

  #[ORM\ManyToOne(inversedBy: 'worktimes')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Employee $employee = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): static
  {
    $this->id = $id;

    return $this;
  }

  public function getStartDate(): ?\DateTimeInterface
  {
    return $this->startDate;
  }

  public function setStartDate(\DateTimeInterface $startDate): static
  {
    $this->startDate = $startDate;
    $this->startDay = clone $startDate;
    $this->startDay = $this->startDay->modify('YYYY-mm-DD');

    return $this;
  }

  public function getEndDate(): ?\DateTimeInterface
  {
    return $this->endDate;
  }

  public function setEndDate(\DateTimeInterface $endDate): static
  {
    $this->endDate = $endDate;

    return $this;
  }

  public function getStartDay(): ?\DateTimeInterface
  {
    return $this->startDay;
  }

  public function getEmployee(): ?Employee
  {
    return $this->employee;
  }

  public function setEmployee(Employee $employee): static
  {
    $this->employee = $employee;

    return $this;
  }
}
