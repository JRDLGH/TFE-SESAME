<?php

namespace App\Entity\Profiling;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Profiling\DeficiencyRepository")
 */
class Deficiency
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $severity;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Profiling\Disabled", mappedBy="deficiencies")
     */
    private $disableds;

    public function __construct()
    {
        $this->disableds = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSeverity(): ?string
    {
        return $this->severity;
    }

    public function setSeverity(?string $severity): self
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * @return Collection|Disabled[]
     */
    public function getDisableds(): Collection
    {
        return $this->disableds;
    }

    public function addDisabled(Disabled $disabled): self
    {
        if (!$this->disableds->contains($disabled)) {
            $this->disableds[] = $disabled;
            $disabled->addDeficiency($this);
        }

        return $this;
    }

    public function removeDisabled(Disabled $disabled): self
    {
        if ($this->disableds->contains($disabled)) {
            $this->disableds->removeElement($disabled);
            $disabled->removeDeficiency($this);
        }

        return $this;
    }
}
