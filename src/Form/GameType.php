<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Game;

use App\Repository\UserRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
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
			->add(
				'nbPlayers',
				IntegerType::class,
				[
					'required' => false,
					'empty_data' => null,
					'label' => 'Nombre de joueurs',
					'attr' => [
						'class' => 'form-control',
						'min' => 0,
						'step'=> 1,
					],
				]
			)
			->add(
				'difficult',
				IntegerType::class,
				[
					'required' => false,
					'label' => 'Difficulté',
					'attr' => [
						'class' => 'form-control',
						'min' => 1,
						'max' => 5,
						'step'=> 1,
					],
				]
			)
			->add(
				'version',
				NumberType::class,
				[
					'required' => true,
					'label' => 'Version',
					'attr' => [
						'min' => 0,
						'class' => 'form-control',
					],
				]
			)
			->add(
				'minAge',
				IntegerType::class,
				[
					'required' => false,
					'label' => 'Âge minimum',
					'attr' => [
						'class' => 'form-control',
						'min' => 0,
						'max' => 126,
						'step'=> 1,
					],
				]
			)
			->add(
				'time',
				TimeType::class,
				[
					// 'widget' => 'single_text',
					// 'data' => new \DateTime('now'),
					'label' => "Durée",
					'required' => false,
					'attr' => [
						'class' => 'form-control',
					],
				]
			)
			->add(
				'owner',
				EntityType::class,
				[
					'class' => User::class,
					'choice_label' => 'userName',
					'required' => false,
					'label' => "Propriétaire",
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'query_builder' => function(UserRepository $e){
						return $e->createQueryBuilder('x')
							->orderBy('x.userName', 'ASC');
					},
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Game::class,
		]);
	}
}
