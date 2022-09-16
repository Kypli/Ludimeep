<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Table;

use App\Repository\GameRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$user_id = $options['user_id'];
		$builder
			->add(
				'gameOwner',
				EntityType::class,
				[
					'class' => Game::class,
					'choice_label' => 'name',
					'required' => false,
					'label' => "Vos jeux",
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'mapped' => false,
					'query_builder' => function(GameRepository $e) use($user_id){
						return $e->createQueryBuilder('x')
							->where('x.owner = :user')
							->setParameter(':user', $user_id)
							->orderBy('x.name', 'ASC')
						;
					},
				]
			)
			->add(
				'gamePresent',
				EntityType::class,
				[
					'class' => Game::class,
					'choice_label' => 'name',
					'required' => false,
					'label' => "Liste de jeux des adhérants inscrit à la séance",
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'mapped' => false,
					'query_builder' => function(GameRepository $e) use($user_id){
						return $e->createQueryBuilder('x')
							->where('x.owner = :user')
							->setParameter(':user', $user_id)
							->orderBy('x.name', 'ASC')
						;
					},
				]
			)
			->add(
				'gameAdherant',
				EntityType::class,
				[
					'class' => Game::class,
					'choice_label' => 'name',
					'required' => false,
					'label' => "Liste de jeux des adhérants",
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'mapped' => false,
					'query_builder' => function(GameRepository $e) use($user_id){
						return $e->createQueryBuilder('x')
							->where('x.owner = :user')
							->setParameter(':user', $user_id)
							->orderBy('x.name', 'ASC')
						;
					},
				]
			)
			// ->add('gameFree')
			// ->add('maxPlayer')
			// ->add('players')
			// ->add('seance')
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Table::class,
			'user_id' => 0,
		]);
	}
}
