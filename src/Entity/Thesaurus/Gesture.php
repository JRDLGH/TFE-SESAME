<?php

namespace App\Entity\Thesaurus;

use App\Entity\Thesaurus\Gesture\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\GestureRepository")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="admin.constraints.gesture.name.unique",
 * )
 */
class Gesture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"list","show"})
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="admin.constraints.gesture.name.type"
     * )
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="admin.constraints.gesture.name.min",
     *     maxMessage="admin.constraints.gesture.name.max",
     * )
     *
     *
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     *
     */
    private $profileVideo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     */
    private $video;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list","show"})
     */
    private $cover;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list","show"})
     * @Assert\Length(
     *     min= "5",
     *     max= "255",
     *     minMessage="admin.constraints.gesture.description.min",
     *     maxMessage="admin.constraints.gesture.description.max",
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="datetime",options={"default":"CURRENT_TIMESTAMP"},nullable=true)
     */
    private $creationDate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Thesaurus\Gesture\Tag", inversedBy="gestures", cascade={"persist"})
     * @Groups({"list"})
     */
    private $tags;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publicationDate;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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

    public function getProfileVideo(): ?string
    {
        return $this->profileVideo;
    }

    public function setProfileVideo(?string $profileVideo): self
    {
        $this->profileVideo = $profileVideo;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }
    /**
     * Triggered on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->creationDate = new \DateTime("now",new \DateTimeZone("Europe/Brussels"));
    }

}
