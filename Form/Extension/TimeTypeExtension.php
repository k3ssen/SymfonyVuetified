<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVuetified\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): array
    {
        return [TimeType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'input' => 'datetime_immutable',
            'type' => 'time',
            'widget' => 'single_text',
            'placeholder' => 'dd-mm-yyyy',
            'input_format' => 'H:i', # https://symfony.com/doc/current/reference/forms/types/time.html#input-format
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $attr = $view->vars['attr'] ?? [];
        $data = $view->vars['data'];
        if ($data instanceof \DateTimeInterface) {
            $view->vars['data'] = $data->format('H:i');
        }
        $attr['type'] = $attr['type'] ?? 'time';
        $view->vars['attr'] = $attr;
    }
}
