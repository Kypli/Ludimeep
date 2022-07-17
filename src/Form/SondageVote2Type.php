<?php

namespace App\Form;

use App\Entity\Sondage;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SondageVote2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'vote',
                ChoiceType::class,
                [
                    'required' => true,
                    'expanded' => true,
                    'multiple' => false,
                    'label' => "Voter",
                    'choices'  => $options['lines'],
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
            'lines' => [],
        ]);
    }
}
