<?php

namespace App\Form;

use App\Entity\Sms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $now = new \DateTime();
        $minDate = $now->format('Y-m-d\TH:i:s');
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'placeholder' => 'Contenu du message...',
                    'class' => 'min-h-150px'
                ]
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'Langues',
                'choices' => [
                    'Automatique' => 'auto',
                    'Français' => 'fr',
                    'Nederlands' => 'nl',
                    'English' => 'en',
                    'Español' => 'es',
                    'Italiano' => 'it',
                ],
                'data' => 'auto',
                'expanded' => true,
                'attr' => [
                    'class' => 'd-flex flex-wrap gap-3 form-control py-3'
                ]
            ])
            ->add('sentAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Planifier l\'envoi',
                'data' => $now,
                'attr' => [
                    'min' => $minDate
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sms::class,
        ]);
    }
}
