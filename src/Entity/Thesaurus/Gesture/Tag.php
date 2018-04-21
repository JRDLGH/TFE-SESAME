<?php

namespace App\Entity\Thesaurus\Gesture;

use App\Entity\Thesaurus\Gesture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @ORM\Column(type="string", length=50, unique=true)
     * @Groups({"list"})
     */
    private $keyword;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Thesaurus\Gesture", mappedBy="tags")
     */
    private $gestures;

    public function __construct()
    {
        $this->gestures = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * @return Collection|Gesture[]
     */
    public function getGestures(): Collection
    {
        return $this->gestures;
    }

    public function addGesture(Gesture $gesture): self
    {
        if (!$this->gestures->contains($gesture)) {
            $this->gestures[] = $gesture;
            $gesture->addTag($this);
        }

        return $this;
    }

    public function removeGesture(Gesture $gesture): self
    {
        if ($this->gestures->contains($gesture)) {
            $this->gestures->removeElement($gesture);
            $gesture->removeTag($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getKeyword();
    }
}
