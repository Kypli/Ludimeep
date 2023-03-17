<?php

namespace App\Form;

use App\Entity\Mandat;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MandatType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'titre',
				TextType::class,
				[
					'required' => true,
					'label' => 'Titre',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'duree',
				IntegerType::class,
				[
					'required' => true,
					'label' => 'Durée (en années)',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'priorite',
				IntegerType::class,
				[
					'required' => true,
					'label' => "Priorité d'affichage",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'required',
				CheckboxType::class,
				[
					'required' => false,
					'label' => 'Mandataire requis',
				]
			)
			->add(
				'uniq',
				CheckboxType::class,
				[
					'required' => false,
					'label' => 'Mandataire unique',
				]
			)
			->add(
				'isActif',
				CheckboxType::class,
				[
					'required' => false,
					'label' => 'Actif',
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Mandat::class,
		]);
	}
}
