<?php

namespace App\Form;

use App\Entity\Beehive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BeehiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de la ruche :'])
            ->add('race', ChoiceType::class, [
                'choices'  => [
                    'Noire' => 'noire',
                    'Caucasienne' => 'caucasienne',
                    'Carnica' => 'carnica',
                    'Italienne' => 'italienne',
                    'Buckfast' => 'buckfast',
                ],
                'multiple'     => false,
                'expanded'     => false,
                'label' => "Race des abeilles :"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Beehive::class,
        ]);
    }
}
