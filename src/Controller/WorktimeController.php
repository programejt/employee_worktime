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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Uid\Uuid;

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

  #[Route('/worktime/summary', name: 'app_worktime_summary', methods: ['GET'])]
  public function summary(
    Request $request,
    ParameterBagInterface $parameterBag,
    WorktimeRepository $worktimeRepository,
    EntityManagerInterface $entityManager,
  ): JsonResponse {
    $employeeUuid = $request->query->get('employee');
    $date = $request->query->get('date');

    if (!($employeeUuid && $date)) {
      return $this->json([
        'response' => 'Nie podano id pracownika i/lub daty',
      ], 400);
    }

    $cost = $parameterBag->get('cost');

    if (\preg_match('/^\d{4}-\d{2}$/', $date)) {
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

      $monthlyHours = $parameterBag->get('monthly_hours');
      $afterHoursCostMultiply = $parameterBag->get('after_hours_cost_multiply');

      $workedHours = 0;

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

      return $this->json([
        'response' => [
          'ilosc normalnych godzin z danego miesiaca' => $monthlyHours,
          'ilosc przepracowanych godzin z danego miesiaca' => $workedHours,
          'stawka' => $cost . ' PLN',
          'ilosc nadgodzin z danego miesiaca' => $afterHours,
          'stawka nadgodzinowa' => $afterHoursCostMultiply * $cost . ' PLN',
          'suma po przeliczeniu' => $toPay . 'PLN',
        ],
      ]);
    }

    $date = new \DateTime($date);

    $worktime = $worktimeRepository->findOneBy([
      'employee' => $employeeUuid,
      'startDay' => $date,
    ]);

    if (!$worktime) {
      return $this->json([
        'response' => 'Nie znaleziono przedziału czasowego dla podanych parametrów',
      ], 404);
    }

    $workedHours = $worktime->getWorkedHours();

    return $this->json([
      'response' => [
        'suma po przeliczeniu' => $workedHours * $cost . ' PLN',
        'ilosc godzin z danego dnia' => $workedHours,
        'stawka' => $cost . ' PLN',
      ],
    ]);
  }
}
