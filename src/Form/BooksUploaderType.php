<?php

namespace App\Form;

use App\Service\FileReader\Types\Csv as CsvFileReader;
use App\Service\FileReader\Types\Json as JsonFileReader;
use App\Service\FileReader\Types\Yaml as YamlFileReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;


class BooksUploaderType extends AbstractType
{
    const ALLOWED_MIME_TYPES = ['text/csv' => CsvFileReader::class, 'application/json' => JsonFileReader::class, 'application/x-yaml' => YamlFileReader::class];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $package = new Package(new EmptyVersionStrategy());

        $builder
            ->add('attachment', FileType::class, [
                'help_html' => true,
                'help' => 'Allowed MIME types: '. implode(', ', array_keys(self::ALLOWED_MIME_TYPES)).'. Use  <a href="'.$package->getUrl('/upload/_testFileSamples.zip').'">sample files</a> as templates ',
            ])
            ->add('Import', SubmitType::class, [
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
