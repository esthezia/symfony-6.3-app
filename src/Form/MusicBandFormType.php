<?php

namespace App\Form;

use App\Entity\MusicBand;
use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class MusicBandFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control-sm'],
                'label' => 'Nom du groupe'
            ])
            ->add('city', CityFormType::class, [
                'by_reference' => false,
                'label' => false,
                'countries' => $options['countries']
            ])
            ->add('genre', TextType::class, [
                'attr' => ['class' => 'form-control-sm'],
                'label' => 'Courant musical',
                'required' => false
            ])
            ->add('members', NumberType::class, [
                'attr' => ['class' => 'form-control-sm'],
                'label' => 'Membres',
                'required' => false
            ])
            ->add('start_year', NumberType::class, [
                'attr' => ['class' => 'form-control-sm'],
                'label' => 'Année début'
            ])
            ->add('end_year', NumberType::class, [
                'attr' => ['class' => 'form-control-sm'],
                'label' => 'Année séparation',
                'required' => false
            ])
            ->add('founder', TextType::class, [
                'attr' => ['class' => 'form-control-sm'],
                'label' => 'Fondateurs',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control-sm mb-4',
                    'rows' => 5
                ],
                'label' => 'Présentation',
                'required' => false
            ])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn-success btn-sm']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MusicBand::class,
            'countries' => []
        ]);
    }
}
