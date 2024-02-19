<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Enum\DeckType as DeckTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DeckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                ]
            )
            ->add(
                'type',
                EnumType::class,
                [
                    'class' => DeckTypeEnum::class,
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                    'required' => true,
                    'label' => 'Publicity'
                ]
            );
    }
}