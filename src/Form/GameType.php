<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Game;

use App\Repository\UserRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('nbPlayers')
            ->add('difficult')
            ->add('version')
            ->add('minAge')
            ->add('time')
            // ->add(
            //     'owner',
            //     EntityType::class,
            //     [
            //         'class' => User::class,
            //         'choice_label' => 'userName',
            //         'required' => false,
            //         'expanded' => false,
            //         'attr' => [
            //             'class' => 'form-control',
            //         ],
            //         'label' => "Propriétaire",
            //         'query_builder' => function(UserRepository $e){
            //             return $e->createQueryBuilder('x')
            //                 ->orderBy('x.id', 'ASC');
            //         },
            //     ]
            // )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
