<?php
//TODO: THIS FILE IS AUTO-GENERATED - REMOVE THIS COMMENT TO MAKE CLEAR THAT YOU'VE REVIEWED THIS FILE
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends AbstractVoter
{
    public const INDEX = 'ADMIN_USER_INDEX';
    public const SEARCH = 'ADMIN_USER_SEARCH';
    public const VIEW = 'ADMIN_USER_VIEW';
    public const CREATE = 'ADMIN_USER_CREATE';
    public const EDIT = 'ADMIN_USER_EDIT';
    public const DELETE = 'ADMIN_USER_DELETE';

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): ?bool
    {
        switch ($attribute) {
            case self::INDEX:
            case self::SEARCH:
            case self::CREATE:
                return $this->isAdmin();
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->isAdmin() && $subject !== null;
        }
        return null;
    }
}