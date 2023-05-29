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
            ->add('email', EmailType::class, ['label' => 'Email address'])
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class, ['label' => 'Log in'])
            ->add('back', ButtonType::class, ['attr' => ['class' => 'btn btn-secondary login-back-button']]);
    }
}