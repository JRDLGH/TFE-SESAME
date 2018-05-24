<?php

namespace App\Entity\Profiling;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Profiling\DisabledRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Disabled
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="profile.constraint.firstname.blank",
     * groups={"Registration"})
     *
     * @Assert\Length(
     *  min=3,
     *  max=255,
     *  minMessage="profile.constraint.firstname.min",
     *  maxMessage="profile.constraint.firstname.max",
     *  groups={"Registration"}
     * )
     *
     * @Assert\Type(type="string",
     *  message="profile.constraint.firstname.type")
     *
     * @Assert\Regex(pattern="/\d/",
     *  match=false,
     *  message="profile.constraint.firstname.invalid_type")
     * @Groups({"list","search"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="profile.constraint.lastname.blank",
     * groups={"Registration"})
     *
     * @Assert\Length(
     *  min=3,
     *  max=255,
     *  minMessage="profile.constraint.lastname.min",
     *  maxMessage="profile.constraint.lastname.max",
     *  groups={"Registration"}
     * )
     *
     * @Assert\Type(type="string",
     *  message="profile.constraint.lastname.type")
     *
     * @Assert\Regex(pattern="/\d/",
     *  match=false,
     *  message="profile.constraint.lastname.invalid_type")
     *
     * @Groups({"list","search"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="date", nullable=true)
     * example of format: 24/01/96 or 24/01/1996
     * @Assert\Date()
     * @Assert\LessThan("today")
     * @Assert\LessThan("-3 years")
     *
     * @Groups({"search"})
     */
    private $birthday;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Profiling\Deficiency", inversedBy="disableds")
     */
    private $deficiencies;

    /**
     * @var Profile
     * @ORM\OneToOne(targetEntity="App\Entity\Profiling\Profile", inversedBy="owner", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"search"})
     */
    private $profile;


    public function __construct()
    {
        $this->deficiencies = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return Collection|Deficiency[]
     */
    public function getDeficiencies(): Collection
    {
        return $this->deficiencies;
    }

    public function addDeficiency(Deficiency $deficiency): self
    {
        if (!$this->deficiencies->contains($deficiency)) {
            $this->deficiencies[] = $deficiency;
        }

        return $this;
    }

    public function removeDeficiency(Deficiency $deficiency): self
    {
        if ($this->deficiencies->contains($deficiency)) {
            $this->deficiencies->removeElement($deficiency);
        }

        return $this;
    }

    /**
     * @return Profile|null
     */
    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Triggered on insert
     * @ORM\PrePersist
     */
    public function setDisabledProfile(){
        $profile = new Profile();
        $this->setProfile($profile);
    }

    public function __toString()
    {
        return $this->getLastname().' '.$this->getFirstname();
    }
}
