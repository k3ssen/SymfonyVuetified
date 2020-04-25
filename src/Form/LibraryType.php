<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Book;
use App\Entity\Library;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
            ->add('locations', null, [
                'row_attr' => [
//                    'style' => 'display: inline-block; width: 200px;'
                ],
                'choice_attr' => function() {
                    return [
                        'hide-details' => true,
                    ];
                },
                'attr' => [
//                    'row' => true,
                ],
//                    [
//                        'style' => 'display: inline-block; width: 200px;',
//                    'hide-details' => true,
//                ],
                'label' => 'Locations',
                'by_reference' => false,
//                'block_prefix' => 'CheckboxGroupType',
//                'expanded' => true,
            ])
            ->add('testLocation', ChoiceType::class, [
                'label' => 'Atest',
                'choices' => [
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C',
                ],
                'data' => 'B',
                'attr' => [
                    'row' => true,
                ],
                'multiple' => false,
                'expanded' => true,
                'mapped' => false,
            ])
            ->add('checkboxtest', CheckboxType::class, [
                'label' => 'Atest',
                'data' => true,
                'block_prefix' => 'SwitchType',
                'mapped' => false,
                'attr' => [
                    'color' => 'red',
                ],
            ])
            ->add('hiddentest', HiddenType::class, [
                'label' => 'Atest',
                'mapped' => false,
                'data' => true,
            ])
            ->add('books', CollectionType::class, [
                'label' => 'Books',
//                'expanded' => true,
                'entry_type' => BookType::class,
                'prototype_data' => new Book(),
                'allow_add' => true,
                'allow_delete' => true,
                'btn_add_txt' => 'add new book',
                'by_reference' => false,
                'block_prefix' => 'CollectionTableType'
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