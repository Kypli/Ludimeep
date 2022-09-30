<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Table;

use App\Repository\GameRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$user_id = $options['user_id'];
		$seance_id = $options['seance_id'];
		$seances_table = $options['seances_table'];

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
					'query_builder' => function(GameRepository $e) use($seance_id, $user_id){
						return $e->createQueryBuilder('x')
							->join('x.owner', 'u')
							->join('u.seances', 's')

							->where('x.owner != :user')

							->where('s.id = :seance_id')
							->setParameter(':seance_id', $seance_id)

							->andWhere('u.id != :user')
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
							->join('x.owner', 'u')
							->where('x.owner != :user')
							->setParameter(':user', $user_id)
							->orderBy('x.name', 'ASC')
						;
					},
				]
			)
			->add(
				'gameFree',
				TextType::class,
				[
					'required' => false,
					'label' => 'Jeux libre',
					'mapped' => false,
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			// ->add(
			// 	'seance',
			// 	ChoiceType::class,
			// 	[
			// 		'required' => true,
			// 		'label' => "Séance",
			// 		'attr' => [
			// 			'class' => 'form-control',
			// 		],
			// 		'choices'  => $options['seances_table'],
			// 	]
			// )
			->add(
				'maxPlayer',
				IntegerType::class,
				[
					'required' => true,
					'empty_data' => null,
					'label' => 'Nombre de joueurs maximum',
					'attr' => [
						'class' => 'form-control',
						'min' => 0,
						'step'=> 1,
					],
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Table::class,
			'user_id' => 0,
			'seance_id' => 0,
			'seances_table' => [],
		]);
	}
}
