<?php

namespace App\DTO;

readonly class WorktimeSummaryByMonth extends WorktimeSummaryByDay
{
  public function __construct(
    float $cost,
    float $workedHours,
    float $toPay,
    public float $monthlyHours,
    public float $afterHours,
    public float $afterHoursCost,
  ) {
    parent::__construct($cost, $workedHours, $toPay);
  }
}
