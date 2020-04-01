<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotNull
     */
    private ?string $name = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Library", inversedBy="locations")
     * @Assert\Valid
     */
    private Collection $libraries;


    public function __construct()
    {
        $this->libraries = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|Library[]
     */
    public function getLibraries(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(Library $library): self
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries->add($library);
            $library->addLocation($this);
        }
        return $this;
    }

    public function removeLibrary(Library $library): self
    {
        if ($this->libraries->contains($library)) {
            $this->libraries->removeElement($library);
            $library->removeLocation($this);
        }
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
