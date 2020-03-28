<?php
declare(strict_types=1);

namespace App\Form\Extension;

use App\Vue\VueDataStorage;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeExtension extends AbstractTypeExtension
{
    /**
     * @var VueDataStorage
     */
    private $vueDataStorage;

    public function __construct(VueDataStorage $vueDataStorage)
    {
        $this->vueDataStorage = $vueDataStorage;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'v-model' => null,
        ]);
    }

    public static function getExtendedTypes(): array
    {
        return [FormType::class];
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if ($view->vars['compound'] ?? true) {
            return;
        }
        $attr = $view->vars['attr'] ?? [];
        if ($options['v-model'] ?? false) {
            if ($attr['v-model'] ?? false) {
                throw new \RuntimeException('option "v-model" should not be defined if it is also defined inside the "attr" option');
            }
            $attr['v-model'] = $options['v-model'];
        }
        $attr['v-model'] = $attr['v-model'] ?? 'form_'.$view->vars['id'];

        $value = $view->vars['value'];
        if (array_key_exists('checked', $view->vars)) {
            $value = $view->vars['checked'] ? "1" : "0";
        }

        $this->vueDataStorage->addData($attr['v-model'], $value);
        $view->vars['attr'] = $attr;
    }
}
