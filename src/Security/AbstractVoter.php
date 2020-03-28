<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * AbstractVoter to share some common methods in all your voters.
 *
 * Implementing VoterInterface instead of extending Symfony's Voter class would be a more logical choice.
 * Unfortunately, the Symfony Plugin in PhpStorm requirers the Voters class to be extended, which
 * makes this a more practical choice.
 */
abstract class AbstractVoter extends Voter
{
    /** @var TokenInterface */
    protected $token;

    /** @var RoleHierarchyInterface|RoleHierarchy */
    protected $roleHierarchy;

    /**
     * @required
     */
    public function setRoleHierarchy(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $this->token = $token;
        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            $vote = $this->voteOnAttribute($attribute, $subject, $token);
            if ($vote === true) {
                return self::ACCESS_GRANTED;
            } elseif ($vote === false) {
                return self::ACCESS_DENIED;
            }
            // Note that if '$vote === null', it will stay 'abstain'
        }

        return $vote;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, (new \ReflectionClass($this))->getConstants(), true);
    }

    protected function isSuperAdmin(): bool
    {
        return $this->hasRole(Roles::ROLE_SUPER_ADMIN);
    }

    protected function isAdmin(): bool
    {
        return $this->hasRole(Roles::ROLE_ADMIN);
    }

    protected function isUser(): bool
    {
        return $this->hasRole(Roles::ROLE_USER);
    }

    protected function hasRole(string $roleName): bool
    {
        foreach ($this->roleHierarchy->getReachableRoleNames($this->getToken()->getRoleNames()) as $role) {
            if ($roleName === $role) {
                return true;
            }
        }
        return false;
    }

    protected function getUser()
    {
        return $this->getToken()->getUser();
    }

    protected function isLoggedIn(): bool
    {
        return $this->getUser() instanceof UserInterface;
    }

    protected function getToken(): TokenInterface
    {
        if (!$this->token) {
            throw new \RuntimeException('Cannot retrieve token before "vote" method has been called.');
        }
        return $this->token;
    }
}
