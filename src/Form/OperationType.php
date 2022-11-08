<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Operation;

use App\Repository\UserRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class OperationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'number',
				NumberType::class,
				[
					'required' => true,
					'label' => 'Montant',
					'attr' => [
						'class' => 'form-control',
						'step' => '0.01',
					],
				]
			)
			->add(
				'comment',
				TextType::class,
				[
					'required' => false,
					'label' => 'Commentaire',
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
					'data' => new \Datetime('now'),
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
					'data' => $options['user'],
					'required' => true,
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'label' => "Adhérant",
					'query_builder' => function(UserRepository $ur)
					{
						return $ur->createQueryBuilder('x')
							->join('x.asso', 'a')
							->where('a.adherant > 0')
							->orderBy('x.userName')
						;
					},
				]
			)
			->add(
				'valid',
				CheckboxType::class,
				[
					'required' => false,
					'label' => 'Valider par admin',
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Operation::class,
			'user' => null,
		]);
	}
}
