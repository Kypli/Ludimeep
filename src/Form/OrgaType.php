<?php

namespace App\Form;

use App\Entity\Organigramme;
use App\Repository\OrganigrammeRepository;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Component\Validator\Constraints\Image;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrgaType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'user',
				EntityType::class,
				[
					'class' => User::class,
					'choice_label' => function (User $user){
						$nom = null !== $user->getProfil()->getNom() && null !== $user->getProfil()->getPreNom()
							? ' ('.ucfirst($user->getProfil()->getPreNom()).' '.ucfirst($user->getProfil()->getNom()).')'
							: ''
						;
						$mandat = null !== $user->getAsso()->getMandat()
							? ' - '.$user->getAsso()->getMandat()->getTitre()
							: ''
						;

        				return $user->getUserName().$nom.$mandat;
        			},
					'required' => true,
					'expanded' => false,
					'attr' => [
						'class' => 'form-control',
					],
					'label' => "Utilisateur",
					'query_builder' => function(UserRepository $u) use($options){

						$q = $u->createQueryBuilder('u')

							// Doit avoir un rôle CA
							->join('u.asso', 'a')
							->where('a.mandat != :null')
							->setParameter('null', '')

							->orderBy('u.id', 'ASC')
						;

						// Ne doit pas déja être actif dans l'organigramme
						// if ($options['nb_orga'] > 0){
						// 	$q
						// 		->leftjoin('u.organigrammes', 'o')
						// 		->andWhere('o.isActif != :true')
						// 		->setParameter('true', true)
						// 	;
						// 	// Error : N'affiche plus ceux qui ne sont pas dans un orga
						// }

						return $q;
					},
				]
			)
			->add(
				'photo',
				FileType::class,
				[
					'required' => true,
					'label' => 'Photo',
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
