<?php

namespace App\Entity\Profiling;

use App\Entity\Thesaurus\Gesture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Profiling\ProfileRepository")
 */
class Profile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Blank()
     * @Assert\Type(
     *     type="string",
     *     message="profile.constraint.content.type"
     * )
     * @Assert\Length(
     *     min=10,
     *     max=255,
     *     minMessage="profile.constraint.content.min",
     *     maxMessage="profile.constraint.content.max",
     * )
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profiling\Disabled",mappedBy="profile", cascade={"persist", "remove"})
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Profiling\ProfileGesture",mappedBy="profile")
     */
    private $learnedGestures;

    public function __construct()
    {
        $this->learnedGestures = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|ProfileGesture[]
     */
    public function getLearnedGestures(): Collection
    {
        return $this->learnedGestures;
    }

    public function addLearnedGesture(ProfileGesture $learnedGesture): self
    {
        if (!$this->learnedGestures->contains($learnedGesture)) {
            $this->learnedGestures[] = $learnedGesture;
        }

        return $this;
    }

    public function removeLearnedGesture(ProfileGesture $learnedGesture): self
    {
        if ($this->learnedGestures->contains($learnedGesture)) {
            $this->learnedGestures->removeElement($learnedGesture);
        }

        return $this;
    }
}
