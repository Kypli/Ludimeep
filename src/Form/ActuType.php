<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Actu;
use App\Repository\UserRepository;

use Symfony\Component\Validator\Constraints\Image;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActuType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'auteur',
				EntityType::class,
				[
					'class' => User::class,
					'choice_label' => 'userName',
					'required' => true,
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'label' => "Auteur",
					'query_builder' => function(UserRepository $e){
						return $e->createQueryBuilder('e')
							->orderBy('e.id', 'ASC')
							->where('e.roles LIKE :role')
							->setParameter('role', '%ROLE_ADMIN%')
						;
					},
				]
			)
			->add(
				'date',
				DateTimeType::class,
				[
					'date_format' => 'dd / MM / yyyy',
					'required' => true,
					'label' => "Date",
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
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
				'text1',
				TextareaType::class,
				[
					'required' => false,
					'label' => 'Texte 1',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'text2',
				TextareaType::class,
				[
					'required' => false,
					'label' => 'Texte 2',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'text3',
				TextareaType::class,
				[
					'required' => false,
					'label' => 'Texte 3',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'text4',
				TextareaType::class,
				[
					'required' => false,
					'label' => 'Texte 4',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'text5',
				TextareaType::class,
				[
					'required' => false,
					'label' => 'Texte 5',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'text6',
				TextareaType::class,
				[
					'required' => false,
					'label' => 'Texte 6',
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'photo1',
				FileType::class,
				[
					'required' => false,
					'label' => 'Photo 1',
					'mapped' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'constraints' => [
						new Image([
							'maxSize' => '5M'
						]),
					],
				]
			)
			->add(
				'photo1Alt',
				TextType::class,
				[
					'required' => false,
					'label' => 'Description',
					'empty_data' => null,
					'attr' => [
						'class' => 'form-control',
						'multiple' => false,
					],
				]
			)
			->add(
				'photo2',
				FileType::class,
				[
					'required' => false,
					'label' => 'Photo 2',
					'mapped' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'constraints' => [
						new Image([
							'maxSize' => '5M'
						]),
					]
				]
			)
			->add(
				'photo2Alt',
				TextType::class,
				[
					'required' => false,
					'label' => 'Description',
					'empty_data' => null,
					'attr' => [
						'class' => 'form-control',
						'multiple' => false,
					],
				]
			)
			->add(
				'photo3',
				FileType::class,
				[
					'required' => false,
					'label' => 'Photo 3',
					'mapped' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'constraints' => [
						new Image([
							'maxSize' => '5M'
						]),
					]
				]
			)
			->add(
				'photo3Alt',
				TextType::class,
				[
					'required' => false,
					'label' => 'Description',
					'empty_data' => null,
					'attr' => [
						'class' => 'form-control',
						'multiple' => false,
					],
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Actu::class,
		]);
	}
}
