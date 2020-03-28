<?php
declare(strict_types=1);

namespace App\Datatable;

interface DatatableInterface extends \JsonSerializable
{
    public function getAjaxUrl(): string;
    /**
     * @return array|DatatableHeader[]
     */
    public function getHeaders(): array;

    public function getItems(): array;

    public function getTotal(): int;

    public function getDatatableOptions(): DatatableOptions;

    public function useAjax(): bool;
}