<?php
declare(strict_types=1);

namespace App\Datatable;

use App\Entity\User;

class UserDatatable extends AbstractDatatable
{
    protected const USE_VOTER_CHECK = true;

    public function buildHeaders(): void
    {
        $this
            ->addHeader('id', [
                'text' => '#',
            ])
            ->addHeader('username', [
                'text' => 'Username',
            ])
            ->addHeader('email', [
                'text' => 'Email',
            ])
//            ->addHeader('roles', [
//                'text' => 'Roles',
//            ])
        ;
    }

    public function getEntity(): string
    {
        return User::class;
    }

    protected function getPrefix(): string
    {
        return 'admin_' . parent::getPrefix();
    }
}