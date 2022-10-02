<?php

namespace App\Form;

use App\Entity\User;

use App\Form\UserAssoType;
use App\Form\UserProfilType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'profil',
				UserProfilType::class
			)
			->add(
				'asso',
				UserAssoType::class
			)
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
					'required' => $options['data']->getId() == null ? true : false,
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
					'data'   => $options['data']->isAdmin(),
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
					'empty_data' => '',
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
