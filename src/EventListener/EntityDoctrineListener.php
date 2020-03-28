<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Interfaces\BlameableInterface;
use App\Entity\Interfaces\TimestampableInterface;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EntityDoctrineListener implements EventSubscriber
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    // this method can only return the event names; you cannot define a custom method to execute when event triggers
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof TimestampableInterface) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
        if ($entity instanceof BlameableInterface) {
            $user = $this->tokenStorage->getToken()->getUser();
            if ($user instanceof User) {
                $entity->setCreatedBy($user);
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof TimestampableInterface) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }
        if ($entity instanceof BlameableInterface) {
            $user = $this->tokenStorage->getToken()->getUser();
            if ($user instanceof User) {
                $entity->setUpdatedBy($user);
            }
        }
    }
}