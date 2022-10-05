<?php

namespace App\Form;

use App\Entity\SeanceLieu;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceLieuType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'name',
				TextType::class,
				[
					'required' => true,
					'label' => 'Nom',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'adresse',
				TextType::class,
				[
					'required' => false,
					'label' => 'Adresse',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'codePostal',
				TextType::class,
				[
					'required' => false,
					'label' => 'Code Postal',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'ville',
				TextType::class,
				[
					'required' => false,
					'label' => 'Ville',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'ville',
				TextType::class,
				[
					'required' => false,
					'label' => 'Ville',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'defaut',
				CheckboxType::class,
				[
					'required' => false,
					'label' => 'Adresse par défaut',
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => SeanceLieu::class,
			'edit' => false,
		]);
	}
}
