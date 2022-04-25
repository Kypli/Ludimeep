<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Photo;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name')
			->add('alt')
			->add('date')
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
