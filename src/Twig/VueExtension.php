<?php
declare(strict_types=1);

namespace App\Twig;

use App\Vue\VueDataStorage;
use App\Vue\VueForm;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VueExtension extends AbstractExtension
{
    /**
     * @var VueDataStorage
     */
    private $vueDataStorage;

    public function __construct(VueDataStorage $vueDataStorage)
    {
        $this->vueDataStorage = $vueDataStorage;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vue_store', [$this, 'addVueData']),
            new TwigFunction('get_vue_store', [$this, 'getVueData']),
            new TwigFunction('vue_form', [$this, 'getVueForm']),
        ];
    }

    /**
     * Add data to vue-instance.
     * Effectively this would be similar to using <script>vue.data = Object.assign(vue.data, {key: value})</script>
     * However, using script within rendered vue components is not allowed.
     * By using this addVueData method the data can be rendered outside the rendered component, using getVueData
     *
     * @param String $key
     * @param $value
     */
    public function addVueData(String $key, $value): void
    {
        $this->vueDataStorage->addData($key, $value);
    }

    public function getVueData(): string
    {
        return $this->vueDataStorage->getJson();
    }

    public function getVueForm($form): VueForm
    {
        if ($form instanceof FormView) {
            return new VueForm($form);
        } else if ($form instanceof FormInterface) {
            return VueForm::create($form);
        }
        throw new \InvalidArgumentException(sprintf('Provided form must be either a FormView or FormInterface; got %s instead', get_class($form)));
    }
}
