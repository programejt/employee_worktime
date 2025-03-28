<?php

namespace App\Form;

use App\Entity\Worktime;
use App\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorktimeType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('employee', EntityType::class, [
        'class' => Employee::class,
        'invalid_message' => 'Selected employee is invalid',
      ])
      ->add('startDate', DateTimeType::class)
      ->add('endDate', DateTimeType::class)
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Worktime::class,
    ]);
  }
}
