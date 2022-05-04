<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Photo;

// use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'name',
				FileType::class,
				[
					'required' => true,
					'label' => 'Photo',
					'data_class' => null,
					'attr' => [
						'class' => 'form-control',
					],
					'constraints' => [
						// new File([
						// 	'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
						// 	'text/x-comma-separated-values', 
						// 	'text/comma-separated-values', 
						// 	'text/x-csv', 
						// 	'text/csv', 
						// 	'text/plain',
						// 	'application/octet-stream', 
						// 	'application/vnd.ms-excel', 
						// 	'application/x-csv', 
						// 	'application/csv', 
						// 	'application/excel', 
						// 	'application/vnd.msexcel', 
						// 	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
						// 	],
						// 	'mimeTypesMessage' => "Ce document n'est pas valide, uniquement des photos (.jpeg, .jpg, .png).",
						// ]),
						new Image([
							'maxSize' => '5M'
						]),
					]
				]
			)
			->add(
				'alt',
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
				'description',
				TextareaType::class,
				[
					'required' => false,
					'label' => 'Description',
					'attr' => [
						'class' => 'form-control',
					],
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
				'user',
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
					// 'query_builder' => function(UserRepository $e){
					// 	return $e->createQueryBuilder('e')
					// 		->orderBy('e.id', 'ASC')
					// 		->where('e.roles LIKE :role')
					// 		->setParameter('role', '%ROLE_ADMIN%')
					// 	;
					// },
				]
			)		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Photo::class,
		]);
	}
}
