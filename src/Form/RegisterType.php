<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'translation_domain' => 'forms',
                    'label' => 'register.email',
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                    'attr' => [
                        'placeholder' => 'Email address',
                    ]
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'translation_domain' => 'forms',
                    'label' => 'register.name',
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                    'attr' => [
                        'placeholder' => 'Full name'
                    ]
                ]
            )
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords must match!',
                'required' => true,
                'first_options' => [
                    'label' => 'register.password.first',
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                    'attr' => [
                        'placeholder' => 'Password',
                    ]
                ],
                'second_options' => [
                    'label' => 'register.password.second',
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                    'attr' => [
                        'placeholder' => 'Repeat password',
                    ]
                ],
                'translation_domain' => 'forms',
            ]);
    }
}