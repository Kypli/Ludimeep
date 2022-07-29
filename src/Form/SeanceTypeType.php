<?php

namespace App\Form;

use App\Entity\SeanceType as SeanceTypeEntity;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceTypeType extends AbstractType
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
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => SeanceTypeEntity::class,
			'edit' => false,
		]);
	}
}
