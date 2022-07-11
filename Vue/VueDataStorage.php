<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVueBundle\Vue;

class VueDataStorage
{
    public const TYPE_DATA = 'data';
    public const TYPE_STORE = 'store';

    protected array $data = [
        self::TYPE_DATA => [],
        self::TYPE_STORE => [],
    ];

    public function addData(string $key, $value, string $type = self::TYPE_DATA): void
    {
        $this->data[$type][$key] = $value;
    }

    public function getData(string $type = self::TYPE_DATA): array
    {
        return $this->data[$type];
    }

    /**
     * @throws \JsonException
     */
    public function getJson(string $type = self::TYPE_DATA): string
    {
        if (!$this->data[$type]) {
            return "{}";
        }
        $dataArray = [];
        foreach ($this->data[$type] as $key => $value) {
            $this->assignArrayByPath($dataArray, $key, $value);
        }
        return json_encode($dataArray, JSON_THROW_ON_ERROR);
    }

    protected function assignArrayByPath(array &$arr, string $path, $value): void
    {
        $keys = explode('.', str_replace(['[', ']'], ['.', ''], $path));

        foreach ($keys as $key) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }
}