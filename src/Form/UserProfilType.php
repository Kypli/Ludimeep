<?php

namespace App\Form;

use App\Entity\UserProfil;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfilType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'nom',
				TextType::class,
				[
					'required' => false,
					'label' => 'Nom',
					'empty_data' => '',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'prenom',
				TextType::class,
				[
					'required' => false,
					'label' => 'Prénom',
					'empty_data' => '',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'mail',
				TextType::class,
				[
					'required' => false,
					'label' => 'Courriel',
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
				'codePostal',
				TextType::class,
				[
					'required' => false,
					'label' => 'Code postal',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'pays',
				TextType::class,
				[
					'required' => false,
					'label' => 'Pays',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'telephone',
				TextType::class,
				[
					'required' => false,
					'label' => 'Téléphone',
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
			'data_class' => UserProfil::class,
		]);
	}
}
