<?php

namespace App\Form;

use App\Entity\Message;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'libelle',
				TextType::class,
				[
					'required' => false,
					'label' => 'Titre',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'description',
				TextareaType::class,
				[
					'required' => true,
					'label' => 'Contenu',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'mail',
				EmailType::class,
				[
					'required' => true,
					'mapped' => false,
					'label' => 'Votre adresse mail',
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
			'data_class' => Message::class,
		]);
	}
}
