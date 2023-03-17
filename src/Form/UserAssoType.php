<?php

namespace App\Form;

use App\Entity\Mandat;
use App\Entity\UserAsso;

use App\Repository\MandatRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAssoType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'adherant',
				IntegerType::class,
				[
					'required' => false,
					'label' => "Numéro d'adhérant",
					'attr' => [
						'class' => 'form-control',
						'min' => 0,
						'step'=> 1,
					],
				]
			)
			->add(
				'droitImage',
				CheckboxType::class,
				[
					'label' => "Droit à l'image",
					'required' => false,
					'attr' => [
						'class' => 'checkType',
					],
				]
			)
			->add(
				'dateInscription',
				DateType::class,
				[
					'widget' => 'single_text',
					'required' => false,
					'label' => "Date d'inscription",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'dateFinAdhesion',
				DateType::class,
				[
					'widget' => 'single_text',
					'required' => false,
					'label' => "Date de fin d'adhésion",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'notoriete',
				TextType::class,
				[
					'required' => false,
					'label' => "Vous avez connu Ludi-Meep' gràce à",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'mandat',
				EntityType::class,
				[
					'class' => Mandat::class,
					'choice_label' => 'titre',
					'required' => false,
					'expanded' => false,
					'label' => "Mandat",
					'attr' => [
						'class' => 'form-control',
					],
					'query_builder' => function(MandatRepository $e){
						return $e->createQueryBuilder('x')
							->where('x.isActif = :true')
							->setParameter(':true', true)
							->orderBy('x.priorite', 'ASC')
						;
					},
				]
			)
			->add(
				'dateFinMandat',
				DateType::class,
				[
					'widget' => 'single_text',
					'required' => false,
					'label' => "Date de fin de mandat",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'membreHonneur',
				CheckboxType::class,
				[
					'label' => "Membre d'honneur",
					'required' => false,
					'attr' => [
						'class' => 'checkType',
					],
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => UserAsso::class,
		]);
	}
}
