<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Employee;
use App\Form\EmployeeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class EmployeeController extends AbstractController
{
  #[Route('/employee/create', name: 'app_employee_create', methods: ['POST'])]
  public function create(
    Request $request,
    EntityManagerInterface $entityManager,
  ): JsonResponse {
    $employee = new Employee;
    $form = $this->createForm(EmployeeType::class, $employee);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($employee);
      $entityManager->flush();

      return $this->json([
        'id' => $employee->getId(),
      ]);
    }

    return $this->json([
      'id' => null,
    ], 422);
  }
}
