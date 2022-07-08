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
        $date = new \Datetime('+1 week');
        $date = $date->format('Y/m/d');
        $date_fin = new \Datetime($date.' 17:00:00');

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
                'start',
                DateTimeType::class,
                [
                    'required' => true,
                    'data' => new \Datetime('now'),
                    'widget' => 'single_text',
                    'label' => 'Date de début',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'end',
                DateTimeType::class,
                [
                    'required' => true,
                    'data' => $date_fin,
                    'widget' => 'single_text',
                    'label' => 'Date de fin',
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
