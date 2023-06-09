<?php

namespace App\Form;

use App\Service\FileReader\CsvFileReader;
use App\Service\FileReader\JsonFileReader;
use App\Service\FileReader\YamlFileReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooksUploaderType extends AbstractType
{
    const ALLOWED_MIME_TYPES = ['text/csv' => CsvFileReader::class, 'application/json' => JsonFileReader::class, 'application/x-yaml' => YamlFileReader::class];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attachment', FileType::class, [
                'help' => 'Allowed MIME types: '. implode(', ', array_keys(self::ALLOWED_MIME_TYPES)),
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
