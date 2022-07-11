<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVueBundle\Vue;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class VueExtension extends AbstractExtension
{
    protected VueDataStorage $vueDataStorage;

    public function __construct(VueDataStorage $vueDataStorage)
    {
        $this->vueDataStorage = $vueDataStorage;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vue_data', [$this, 'addVueData']),
            new TwigFunction('get_vue_data', [$this, 'getVueData']),
            new TwigFunction('vue_store', [$this, 'addVueStore']),
            new TwigFunction('get_vue_store', [$this, 'getVueStore']),
            new TwigFunction('vue', [$this, 'addVueDataProp']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('vue', [$this, 'addVueDataPropFilter'], ['needs_context' => true]),
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

    /**
     * Same as addVueData, but returns the key
     */
    public function addVueDataProp(String $key, $value): string
    {
        $this->vueDataStorage->addData($key, $value);
        return $key;
    }

    public function addVueDataPropFilter(array $context, $value, ?string $key = null): string
    {
        // Default behaviour would be same as using {{ vue_prop(key, value) }}
        if ($key) {
            return $this->addVueDataProp($key, $value);
        }
        // If there's no key and the value is a string, then treat the value as a key and use null as value.
        if (is_string($value)) {
            return $this->addVueDataProp($value, null);
        }
        // if there's no key and the value is an object, then use the key found in context.
        if (is_object($value)) {
            foreach ($context as $contextKey => $contextValue) {
                if ($value === $contextValue) {
                    return $this->addVueDataProp($contextKey, $value);
                }
            }
        }
        // If so far no key could be determined, then generate a random key.
        $key = 'vue_prop_' . bin2hex(random_bytes(5));
        return $this->addVueDataProp($key, $value);
    }

    public function getVueData(): string
    {
        return $this->vueDataStorage->getJson();
    }

    public function addVueStore(String $key, $value): void
    {
        $this->vueDataStorage->addData($key, $value, VueDataStorage::TYPE_STORE);
    }

    public function getVueStore(): string
    {
        return $this->vueDataStorage->getJson(VueDataStorage::TYPE_STORE);
    }
}
