<?php

namespace App\DTO;

readonly class WorktimeSummaryByDay
{
  public function __construct(
    public float $cost,
    public float $workedHours,
    public float $toPay,
  ) {
  }
}
