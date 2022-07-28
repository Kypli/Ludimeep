<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Seance;
use App\Entity\SeanceType as SeanceTypeEntity;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'date',
				DateTimeType::class,
				[
					'date_format' => 'dd / MM / yyyy',
					'required' => true,
					'label' => "Date",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add('duree')
			->add(
				'type',
				EntityType::class,
				[
					'class' => SeanceTypeEntity::class,
					'choice_label' => 'name',
					'required' => true,
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'label' => "Type",
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Seance::class,
		]);
	}
}
