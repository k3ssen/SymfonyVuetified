<?php
declare(strict_types=1);

namespace App\Datatable;

use Symfony\Component\HttpFoundation\Request;

class DatatableOptions
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var string
     */
    private $search;

    /**
     * @var array|string[]
     */
    private $sortBy;

    /**
     * @var array|boolean[]
     */
    private $sortDesc;

    /**
     * @var int
     */
    private $itemsPerPage;

    public function __construct(Request $request, int $defaultItemsPerPage = 5)
    {
        $options = json_decode($request->getContent(), true);
        $this->search = $options['search'] ?? null;
        $this->page = $options['page'] ?? 1;
        $this->sortBy = $options['sortBy'] ?? [];
        $this->sortDesc = $options['sortDesc'] ?? [];
        $this->itemsPerPage = $options['itemsPerPage'] ?? $defaultItemsPerPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getSortBy(): array
    {
        return $this->sortBy;
    }

    public function getSortDesc(): array
    {
        return $this->sortDesc;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }
}