<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder

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
					'required' => true,
					'label' => 'Mot de passe',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add('droitImage')
			->add('newsletter')

			//------------------
			// Profil
			//------------------
			->add('nom')
			->add('prenom')
			->add('mail')
			->add('adresse')
			->add('telephone')

			//------------------
			// Association
			//------------------
			->add('adherant')
			->add('notoriete')
			->add('roleCa')
			->add('dateFinMandat')
			->add('membreHonneur')
			->add('commentaire')
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
