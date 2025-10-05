<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Nom complet du contact',
                    'example' => 'John Doe'
                ]
            ])
            ->add('email', EmailType::class, [
                'documentation' => [
                    'type' => 'string',
                    'format' => 'email',
                    'description' => 'Adresse email',
                    'example' => 'john@example.com'
                ]
            ])
            ->add('subject', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Sujet du message',
                    'example' => 'Demande de renseignements'
                ]
            ])
            ->add('message', TextareaType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Contenu du message',
                    'example' => 'Bonjour, je voudrais...'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'csrf_protection' => false,
        ]);
    }
}
