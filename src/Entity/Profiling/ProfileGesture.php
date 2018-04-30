<?php

namespace App\Entity\Profiling;

use App\Entity\Thesaurus\Gesture;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfileGestureRepository")
 * @ORM\Table(name="profile_gesture")
 */
class ProfileGesture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profiling\Profile",inversedBy="learnedGestures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Thesaurus\Gesture")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gesture;

    /**
     * @ORM\Column(type="date")
     */
    private $learningDate;

    public function getId()
    {
        return $this->id;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getGesture(): ?Gesture
    {
        return $this->gesture;
    }

    public function setGesture(Gesture $gesture): self
    {
        $this->gesture = $gesture;

        return $this;
    }

    public function getLearningDate(): ?\DateTimeInterface
    {
        return $this->learningDate;
    }

    public function setLearningDate(\DateTimeInterface $learningDate): self
    {
        $this->learningDate = $learningDate;

        return $this;
    }
}
