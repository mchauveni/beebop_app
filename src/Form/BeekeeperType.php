<?php

namespace App\Form;

use App\Entity\Beekeeper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class BeekeeperType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('last_name', TextType::class,  ['label' => 'Votre nom :'])
            ->add('first_name', TextType::class,  ['label' => 'Votre prénom :'])
            ->add('login', TextType::class,  ['label' => 'Votre identifiant :'])
            ->add('mail', EmailType::class, ['label' => 'Votre e-mail :'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe sont différents.',
                'options' => ['attr' => ['class' => 'form__password']],
                'required' => false,
                'first_options'  => ['label' => 'Votre mot de passe : '],
                'second_options' => ['label' => 'Confirmer votre mot de passe : '],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Beekeeper::class,
        ]);
    }
}