<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ImportDeckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'translation_domain' => 'forms',
                    'multiple' => false,
                    'label' => 'decks.import.file',
                    'required' => true,
                    'attr' => [
                        'accept' => '.json, .csv, .xml, .yaml, .yml',
                    ],
                ]
            );
    }
}