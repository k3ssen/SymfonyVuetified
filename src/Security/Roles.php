<?php
declare(strict_types=1);

namespace App\Security;

final class Roles
{
    public const ROLE_USER  = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    private static $roles = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
        self::ROLE_SUPER_ADMIN,
    ];

    public static function getRoles(): array
    {
        return static::$roles;
    }

    public static function getRoleChoices(): array
    {
        return [
            self::ROLE_USER,
            self::ROLE_ADMIN,
        ];
    }
}
