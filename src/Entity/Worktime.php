<?php

namespace App\Entity;

use App\Repository\WorktimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: ['employee', 'startDay'], message: 'Pracownik nie może mieć więcej niż jednego czasu pracy w danym dniu')]
#[ORM\Entity(repositoryClass: WorktimeRepository::class)]
class Worktime
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[Assert\NotBlank]
  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $startDate = null;

  #[Assert\NotBlank]
  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $endDate = null;

  #[ORM\Column(type: Types::DATE_MUTABLE)]
  private ?\DateTimeInterface $startDay = null;

  #[Assert\NotBlank]
  #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'worktimes')]
  #[ORM\JoinColumn(nullable: true)]
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
    $this->startDay = (clone $startDate)->setTime(0, 0);

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

  public function getWorkedHours(): float
  {
    if ($this->getStartDate() && $this->getEndDate()) {
      return round((abs($this->getEndDate()->getTimestamp() - $this->getStartDate()->getTimestamp()) / 3600 * 2)) / 2;
    }

    return 0;
  }
}
