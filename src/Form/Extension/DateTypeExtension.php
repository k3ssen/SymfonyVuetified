<?php
declare(strict_types=1);

namespace App\Form\Extension;

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
            'type' => 'date',
            'widget' => 'single_text',
            'placeholder' => 'dd-mm-yyyy',
            'format' => 'yyyy-MM-dd', # http://symfony.com/doc/current/reference/forms/types/date.html#format
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $attr = $view->vars['attr'] ?? [];
        if ($options['widget'] === 'single_text') {
            $attr['placeholder'] = is_array($options['placeholder']) ? array_pop($options['placeholder']) : $options['placeholder'];
        }
        // note that v-model is also required; this is already being set in TypeExtension
        $attr['v-on'] = "on";
        $attr['prepend-inner-icon'] = "mdi-calendar";
        $view->vars['attr'] = $attr;
    }
}