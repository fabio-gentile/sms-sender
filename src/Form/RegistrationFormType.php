<?php

namespace App\Form;

use App\Entity\User;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, [
                    'label' => 'Adresse email',
                    'attr' => ['placeholder' => 'John']
                    ])
               ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Le mot de passe doit être identique',
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmer le mot de passe'],
                   'options' => ['attr' => ['placeholder' => 'Mot de passe', 'class' => 'password-field']
                ]])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'John']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => ['placeholder' => 'Doe']
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'John']
            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'label' => 'Numéro de téléphone',
                'number_options' => ['attr' => ['placeholder' => '497123456']],
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_choices' => ['GB', 'BE', 'DE', 'FR', 'IT', 'ES'],
                'preferred_country_choices' => ['BE', 'FR'],
                'country_display_emoji_flag' => true,
                'default_region' => 'FR',
                'invalid_message' => 'Veuillez renseigner un numéro de téléphone correct',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
