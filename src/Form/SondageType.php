<?php

namespace App\Form;

use App\Entity\Sondage;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SondageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
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
                'line1',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Champs 1',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'line2',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Champs 2',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'line3',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Champs 3',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'line4',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Champs 4',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'line5',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Champs 5',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'line6',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Champs 6',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'line7',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Champs 7',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'line8',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Champs 8',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'date_debut',
                DateTimeType::class,
                [
                    'required' => false,
                    'label' => 'Date début',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'date_fin',
                DateTimeType::class,
                [
                    'required' => true,
                    'label' => 'Date fin',
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
            'data_class' => Sondage::class,
        ]);
    }
}
