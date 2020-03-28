<?php
declare(strict_types=1);

namespace App\Entity\Interfaces;

/**
 * Alternative to Gedmo\Timestampable\Timestampable, since that interface does not promise any method.
 * This interface can be implemented by using TimestampableEntityTrait
 */
interface TimestampableInterface
{
    /**
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt);

    public function getCreatedAt(): ?\DateTimeImmutable;

    /**
     * @return static
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt);

    public function getUpdatedAt(): ?\DateTimeImmutable;
}