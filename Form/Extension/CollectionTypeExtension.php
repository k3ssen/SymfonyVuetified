<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVuetified\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): array
    {
        return [CollectionType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'btn_add_txt' => 'Add',
            'btn_delete_txt' => 'Remove',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['btn_add_txt'] = $options['btn_add_txt'];
        $view->vars['btn_delete_txt'] = $options['btn_delete_txt'];
    }
}