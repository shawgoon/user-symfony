<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name')
        ->add('firstname')
        ->add('birthday',DateType::class,[
            "label"=>'date de naissance',
            "widget"=>'single_text'
        ])
        ->add('avatar',FileType::class,[
            'label'=>"photo de profil (jpg, png)",
            'data_class'=>null,
            'required'=>false
        ])
        ->add('email',EmailType::class)
        ->add('password',PasswordType::class)
        // ->add('confirmPassword',PasswordType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
