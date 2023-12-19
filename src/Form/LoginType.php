<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'translation_domain' => 'forms',
                    'label' => 'login.email_address',
                    'attr' => [
                        'placeholder' => 'Email address',
                    ],
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ]
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'translation_domain' => 'forms',
                    'label' => 'login.password',
                    'attr' => [
                        'placeholder' => 'Password',
                    ],
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ]
                ]
            );
    }
}