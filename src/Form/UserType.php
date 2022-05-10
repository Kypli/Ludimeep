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
					'label' => 'Pseudo',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'password',
				PasswordType::class,
				[
					'required' => true,
					'label' => 'Mot de passe',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'admin',
				CheckboxType::class,
				[
					'label' => 'Admin',
					'required' => false,
					'data'   => in_array('ROLE_ADMIN', $options['data']->getRoles()) ? true : false,
					'attr' => [
						'class' => 'checkType',
					],
					'mapped' => false,
				]
			)
			->add(
				'anonyme',
				CheckboxType::class,
				[
					'label' => 'Anonyme',
					'required' => false,
					'attr' => [
						'class' => 'checkType',
					],
				]
			)
			->add(
				'ip',
				TextType::class,
				[
					'label' => 'Adresse IP',
					'required' => false,
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'accesPhoto',
				CheckboxType::class,
				[
					'label' => "Droit de poster des images",
					'required' => false,
					'attr' => [
						'class' => 'checkType',
					],
				]
			)
			->add(
				'accesPhotoLanceurAlerte',
				CheckboxType::class,
				[
					'label' => "Droit de signaler des images",
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
