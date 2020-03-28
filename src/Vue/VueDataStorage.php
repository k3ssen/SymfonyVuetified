<?php
declare(strict_types=1);

namespace App\Vue;

class VueDataStorage
{
    protected $vueData = [];

    public function addData(String $key, $value): void
    {
        $this->vueData[$key] = $value;
    }

    public function getData(): array
    {
        return $this->vueData;
    }

    public function getJson(): string
    {
        if (!$this->vueData) {
            return "{}";
        }
        $dataArray = [];
        foreach ($this->vueData as $key => $value) {
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