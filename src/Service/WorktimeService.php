<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Worktime;
use App\DTO\WorktimeSummaryByDay;
use App\DTO\WorktimeSummaryByMonth;

class WorktimeService
{
  public function __construct(
    private readonly ParameterBagInterface $parameterBag,
  ) {
  }

  public function getSummaryByDay(
    Worktime $worktime,
  ): WorktimeSummaryByDay {
    $cost        = $this->parameterBag->get('cost');
    $workedHours = $worktime->getWorkedHours();

    return new WorktimeSummaryByDay(
      $cost,
      $workedHours,
      $workedHours * $cost,
    );
  }

  public function getSummaryByMonth(
    array $worktimes,
  ): WorktimeSummaryByMonth {
    $cost                   = $this->parameterBag->get('cost');
    $monthlyHours           = $this->parameterBag->get('monthly_hours');
    $afterHoursCostMultiply = $this->parameterBag->get('after_hours_cost_multiply');
    $workedHours            = 0;

    foreach ($worktimes as $worktime) {
      $workedHours += $worktime->getWorkedHours();
    }

    $afterHours = $workedHours - $monthlyHours;

    if ($afterHours > 0) {
      $toPay = ($monthlyHours * $cost) + ($afterHours * $cost * $afterHoursCostMultiply);
    } else {
      $toPay = $workedHours * $cost;
      $afterHours = 0;
    }

    return new WorktimeSummaryByMonth(
      $cost,
      $workedHours,
      $toPay,
      $monthlyHours,
      $afterHours,
      $afterHoursCostMultiply * $cost,
    );
  }
}
