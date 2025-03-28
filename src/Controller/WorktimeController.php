<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Worktime;
use App\Form\WorktimeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    $errors = $form->getErrors(true, false);
    $errorsMessages = [];

    foreach ($errors as $error) {
      $errorsMessages[] = $error->getMessage();
    }

    return $this->json([
      'response' => 'Formularz nie został zatwierdzony lub poprawnie zwalidowany',
      'errors' => $errorsMessages,
    ], 422);
  }
}
