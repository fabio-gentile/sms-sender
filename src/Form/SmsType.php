<?php

namespace App\Form;

use App\Entity\Sms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $now = new \DateTime();
        $minDate = $now->modify('-2 minute')->format('Y-m-d\TH:i');
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
                    'English' => 'en-US',
                    'Español' => 'es',
                    'Italiano' => 'it',
                ],
                'data' => 'auto',
                'expanded' => true,
                'attr' => [
                    'class' => 'd-flex flex-wrap gap-3 form-control py-3'
                ],
                'invalid_message' => 'Veuillez renseigner une langue valide.'
            ])
            ->add('scheduledAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Planifier l\'envoi',
                'data' => new \DateTime(),
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
