<?php

namespace App\Form;

use App\Entity\Apiary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ApiaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,  ['label' => 'Nom du rucher :'])
            ->add('localisation', TextType::class,  ['label' => 'Adresse du rucher :'])
            ->add('zipCode', NumberType::class, ['label' => 'Code postal du rucher : ']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Apiary::class,
        ]);
    }
}
