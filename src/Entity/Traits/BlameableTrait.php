<?php
declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

trait BlameableTrait
{
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    protected $createdBy;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    protected $updatedBy;

    /**
     * Sets the User that created the record.
     *
     * @param  UserInterface $user
     * @return $this
     */
    public function setCreatedBy(?UserInterface $user)
    {
        $this->createdBy = $user;

        return $this;
    }

    /**
     * Returns the User that created the record.
     *
     * @return UserInterface
     */
    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    /**
     * Sets the User that updated the record.
     *
     * @param  UserInterface $user
     * @return $this
     */
    public function setUpdatedBy(?UserInterface $user)
    {
        $this->updatedBy = $user;

        return $this;
    }

    /**
     * Returns the User that updated the record.
     *
     * @return UserInterface
     */
    public function getUpdatedBy(): ?UserInterface
    {
        return $this->updatedBy;
    }
}
