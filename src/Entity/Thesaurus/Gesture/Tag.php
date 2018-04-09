<?php

namespace App\Entity\Thesaurus\Gesture;

use App\Entity\Thesaurus\Gesture;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Thesaurus\Gesture\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Thesaurus\Gesture", inversedBy="tags")
     */
    private $gestures;

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

    public function getGestures(): ?Gesture
    {
        return $this->gestures;
    }

    public function setGestures(?Gesture $gestures): self
    {
        $this->gestures = $gestures;

        return $this;
    }
}
