<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Salle;
use App\Entity\Formateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;




class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('DateHeureDebut', DateTimeType::class, [

                'time_label' => 'Start On'          
                ])

            ->add('duration', DateIntervalType::class, [      
               
                'label' => 'Duree',
                'labels' => [
                    
                    'hours' =>'heure',
                    
                ],
                'widget' => 'choice',
                'with_years'  => false,
                'with_months' => false,
                'with_days'   => false,
                'with_hours'=>true,
                'with_minutes'=>false,
                'hours' => range(0,4),
                'mapped'=>false,
                ]) 
            
            ->add('TitreEvenements')

            ->add('salle', EntityType::class, [
                'class'  => Salle::class,
                'choice_label'=>'libelleSalle',
            ])
            
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }

}