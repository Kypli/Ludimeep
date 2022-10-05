<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Seance;
use App\Entity\SeanceLieu;
use App\Entity\SeanceType as SeanceTypeEntity;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
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
					'required' => true,
					'date_format' => 'dd / MM / yyyy',
					'label' => "Date",
					'data' => $options['edit'] ? $options['data']->getDate() : new \Datetime('next tuesday 18:30:00'),
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'duree',
				TimeType::class,
				[
					'required' => false,
					'label' => "Durée",
					'data' => $options['edit'] ? $options['data']->getDuree() : new \Datetime('4:30:00'),
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
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
			->add(
				'lieu',
				EntityType::class,
				[
					'class' => SeanceLieu::class,
					'choice_label' => 'name',
					'required' => true,
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'label' => "Lieu",
				]
			)
			->add(
				'comment',
				TextType::class,
				[
					'required' => false,
					'label' => 'Commentaire',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Seance::class,
			'edit' => false,
		]);
	}
}
