<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Worktime;
use App\Form\WorktimeType;
use App\Repository\WorktimeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use App\Service\WorktimeService;

final class WorktimeController extends AbstractController
{
  #[Route('/worktime/create', name: 'app_worktime_create', methods: ['POST'])]
  public function create(
    Request $request,
    EntityManagerInterface $entityManager,
  ): JsonResponse {
    $worktime = new Worktime;
    $form = $this->createForm(WorktimeType::class, $worktime);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if ($worktime->getWorkedHours() <= 12) {
        try {
          $entityManager->persist($worktime);
          $entityManager->flush();
        } catch (\Exception $e) {
          return $this->json([
            'response' => 'Nie udało się dodać czasu pracy',
          ], 500);
        }

        return $this->json([
          'response' => 'Pomyślnie dodano czas pracy',
        ]);
      }

      $form->addError(new FormError('Przedział czasowy nie może trwać dłużej niż 12 godzin'));
    }

    $errors = $form->getErrors(true, true);
    $errorsMessages = [];

    foreach ($errors as $error) {
      $errorsMessages[] = $error->getMessage();
    }

    return $this->json([
      'response' => 'Formularz nie został zatwierdzony lub poprawnie zwalidowany',
      'errors' => $errorsMessages,
    ], 422);
  }

  #[Route('/worktime/summary/day', name: 'app_worktime_summary_day', methods: ['GET'])]
  public function summaryDay(
    Request $request,
    WorktimeRepository $worktimeRepository,
    WorktimeService $worktimeService,
  ): JsonResponse {
    $employeeUuid = $request->query->get('employee');
    $date         = $request->query->get('date');

    if (!($employeeUuid && $date)) {
      return $this->json([
        'response' => 'Nie podano id pracownika i/lub daty',
      ], 400);
    }

    if (!\preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
      return $this->json([
        'response' => 'Nieprawidłowa data',
      ], 422);
    }

    try {
      $employeeUuid = Uuid::fromString($employeeUuid);
    } catch (\Exception $e) {
      return $this->json([
        'response' => 'Niepoprawne id pracownika',
      ], 422);
    }

    $date = new \DateTime($date);

    $worktime = $worktimeRepository->findOneBy([
      'employee' => $employeeUuid->toBinary(),
      'startDay' => $date,
    ]);

    if (!$worktime) {
      return $this->json([
        'response' => 'Nie znaleziono przedziału czasowego dla podanych parametrów',
      ], 404);
    }

    $summary = $worktimeService->getSummaryByDay($worktime);

    return $this->json([
      'response' => [
        'suma po przeliczeniu' => $summary->toPay . ' PLN',
        'ilość godzin z danego dnia' => $summary->workedHours,
        'stawka' => $summary->cost . ' PLN',
      ],
    ]);
  }

  #[Route('/worktime/summary/month', name: 'app_worktime_summary_month', methods: ['GET'])]
  public function summaryByMonth(
    Request $request,
    WorktimeRepository $worktimeRepository,
    WorktimeService $worktimeService,
  ): JsonResponse {
    $employeeUuid = $request->query->get('employee');
    $date         = $request->query->get('date');

    if (!($employeeUuid && $date)) {
      return $this->json([
        'response' => 'Nie podano id pracownika i/lub daty',
      ], 400);
    }

    if (!\preg_match('/^\d{4}-\d{2}$/', $date)) {
      return $this->json([
        'response' => 'Nieprawidłowa data',
      ], 422);
    }

    try {
      $employeeUuid = Uuid::fromString($employeeUuid);
    } catch (\Exception $e) {
      return $this->json([
        'response' => 'Niepoprawne id pracownika',
      ], 422);
    }

    $dateExplode = \explode('-', $date);

    $worktimes = $worktimeRepository->findByMonthAndEmployee($dateExplode[0], $dateExplode[1], $employeeUuid);

    if (!$worktimes) {
      return $this->json([
        'response' => 'Nie znaleziono czasu pracy dla podanych parametrów',
      ], 404);
    }

    $summary = $worktimeService->getSummaryByMonth($worktimes);

    return $this->json([
      'response' => [
        'ilość normalnych godzin z danego miesiąca' => $summary->monthlyHours,
        'ilość przepracowanych godzin z danego miesiąca' => $summary->workedHours,
        'stawka' => $summary->cost . ' PLN',
        'ilość nadgodzin z danego miesiąca' => $summary->afterHours,
        'stawka nadgodzinowa' => $summary->afterHoursCost . ' PLN',
        'suma po przeliczeniu' => $summary->toPay . ' PLN',
      ],
    ]);
  }
}
