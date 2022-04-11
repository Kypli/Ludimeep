<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder

			//------------------
			// Profil
			//------------------
			->add(
				'nom',
				TextType::class,
				[
					'required' => false,
					'label' => 'Nom',
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

			//------------------
			// Site internet
			//------------------
			->add(
				'userName',
				TextType::class,
				[
					'required' => true,
					'label' => 'Login',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'password',
				PasswordType::class,
				[
					'required' => false,
					'label' => 'Mot de passe',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'roles',
				TextType::class,
				[
					'required' => false,
					'label' => 'Rôles',
					'attr' => [
						'class' => 'form-control',
					],
					'mapped' => false,
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
				'newsletter',
				CheckboxType::class,
				[
					'label' => "Recevoir les newsletters",
					'required' => false,
					'attr' => [
						'class' => 'checkType',
					],
				]
			)

			//------------------
			// Association
			//------------------
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
				'dateInscription',
				DateType::class,
				[
					'widget' => 'single_text',
					'required' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'label' => "Date d'inscription",
				]
			)
			->add(
				'dateFinAdhesion',
				DateType::class,
				[
					'widget' => 'single_text',
					'required' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'label' => "Date de fin d'adhésion",
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
				'roleCa',
				TextType::class,
				[
					'required' => false,
					'label' => "Rôle du CA",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'dateFinMandat',
				DateType::class,
				[
					'widget' => 'single_text',
					'required' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'label' => "Date de fin de mandat",
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
			->add(
				'commentaire',
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
			'data_class' => User::class,
		]);
	}
}
