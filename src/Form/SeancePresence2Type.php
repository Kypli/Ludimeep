<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Seance;
use App\Entity\SeanceType as SeanceTypeEntity;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeancePresence2Type extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{

	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Seance::class,
		]);
	}
}
