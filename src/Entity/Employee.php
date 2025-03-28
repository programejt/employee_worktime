<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
  const int NAME_MAXLENGTH = 60;
  const int SURNAME_MAXLENGTH = 100;

  #[ORM\Id]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  private ?Uuid $id = null;

  #[Assert\NotBlank]
  #[Assert\Length(
    min: 2,
    max: self::NAME_MAXLENGTH,
  )]
  #[ORM\Column(length: self::NAME_MAXLENGTH)]
  private ?string $name = null;

  #[Assert\NotBlank]
  #[Assert\Length(
    min: 2,
    max: self::SURNAME_MAXLENGTH,
  )]
  #[ORM\Column(length: self::SURNAME_MAXLENGTH)]
  private ?string $surname = null;

  /**
   * @var Collection<int, Worktime>
   */
  #[ORM\OneToMany(targetEntity: Worktime::class, mappedBy: 'employee', orphanRemoval: true)]
  private Collection $worktimes;

  public function __construct()
  {
    $this->worktimes = new ArrayCollection();
    $this->id = Uuid::v7();
  }

  public function getId(): ?Uuid
  {
    return $this->id;
  }

  public function setId(Uuid $id): static
  {
    $this->id = $id;

    return $this;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;

    return $this;
  }

  public function getSurname(): ?string
  {
    return $this->surname;
  }

  public function setSurname(string $surname): static
  {
    $this->surname = $surname;

    return $this;
  }

  /**
   * @return Collection<int, Worktime>
   */
  public function getWorktimes(): Collection
  {
    return $this->worktimes;
  }

  public function addWorktime(Worktime $worktime): static
  {
    if (!$this->worktimes->contains($worktime)) {
      $this->worktimes->add($worktime);
      $worktime->setEmployee($this);
    }

    return $this;
  }

  public function removeWorktime(Worktime $worktime): static
  {
    if ($this->worktimes->removeElement($worktime)) {
      // set the owning side to null (unless already changed)
      if ($worktime->getEmployee() === $this) {
        // $worktime->setEmployee(null);
      }
    }

    return $this;
  }
}
