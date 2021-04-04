<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVuetified\Vue;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class VueDataStorage
{
    public const TYPE_DATA = 'data';
    public const TYPE_STORE = 'store';

    protected $data = [
        self::TYPE_DATA => [],
        self::TYPE_STORE => [],
    ];

    public function addData(string $key, $value, string $type = self::TYPE_DATA): void
    {
        if ($value instanceof FormView) {
            $value = new VueForm($value);
        } else if ($value instanceof FormInterface) {
            $value = VueForm::create($value);
        }
        $this->data[$type][$key] = $value;
    }

    public function getData(string $type = self::TYPE_DATA): array
    {
        return $this->data[$type];
    }

    public function getJson(string $type = self::TYPE_DATA): string
    {
        if (!$this->data[$type]) {
            return "{}";
        }
        $dataArray = [];
        foreach ($this->data[$type] as $key => $value) {
            $this->assignArrayByPath($dataArray, $key, $value);
        }
        return json_encode($dataArray);
    }

    protected function assignArrayByPath(&$arr, $path, $value) {
        $keys = explode('.', str_replace(['[', ']'], ['.', ''], $path));

        foreach ($keys as $key) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }
}