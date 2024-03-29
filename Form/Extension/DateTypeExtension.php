<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVuetified\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): array
    {
        return [DateType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $attr = $view->vars['attr'] ?? [];
        $data = $view->vars['data'];
        if ($data instanceof \DateTimeInterface) {
            $view->vars['data'] = $data->format('Y-m-d');
        }
        $view->vars['attr'] = $attr;
    }
}
