<?php
declare(strict_types=1);

namespace App\Datatable;

use App\Entity\Library;

class LibraryDatatable extends AbstractDatatable
{
    protected const USE_VOTER_CHECK = false;

    public function buildHeaders(): void
    {
        $this
            ->addHeader('id', [
                'text' => '#',
            ])
            ->addHeader('name', [
                'text' => 'Name',
            ])
        ;
    }

    public function getEntity(): string
    {
        return Library::class;
    }
}