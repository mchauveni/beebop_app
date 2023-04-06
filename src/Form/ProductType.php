<?php

namespace App\Form;

use NumberFormatter;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Miel' => 'miel',
                    'Cire' => 'cire',
                    'Pollen' => 'pollen',
                ],
                'multiple' => false,
                'expanded' => false,
                'label' => 'Type de produits récoltés : ',
            ])
            ->add('quantity', NumberType::class,[
                'scale' => 2,
                'label' => 'Quantité du produit (en kg) :'
            ])
            ->add('date', DateType::class, [
                'input' => 'datetime_immutable', 'widget' => 'single_text',
                'label' => 'Date de la collecte :'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
