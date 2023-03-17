<?php

namespace App\Form;

use App\Entity\Organigramme;
use App\Repository\OrganigrammeRepository;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Component\Validator\Constraints\Image;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrgaEditType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'end',
				DateType::class,
				[
					'required' => true,
					'label' => "Date de fin de mandat",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'comment',
				TextareaType::class,
				[
					'required' => false,
					'label' => 'Commentaire',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'isActif',
				CheckboxType::class,
				[
					'required' => false,
					'label' => 'Actif',
					'data' => true,
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'nb_orga' => 0, 
			'data_class' => Organigramme::class,
		]);
	}
}
