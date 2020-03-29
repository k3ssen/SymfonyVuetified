<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Book;
use App\Entity\Library;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LibraryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Name',
            ])
            ->add('date', null, [
                'label' => 'Date',
            ])
            ->add('books', CollectionType::class, [
                'label' => 'Books',
                'entry_type' => BookType::class,
                'prototype_data' => new Book(),
                'allow_add' => true,
                'allow_delete' => true,
                'btn_add_txt' => 'add new book',
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Library::class,
        ]);
    }
}