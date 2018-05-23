<?php

namespace App\Entity\Thesaurus;

use App\Entity\Thesaurus\Gesture\Tag;
use App\Validator\PublishableGesture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\GestureRepository")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="admin.constraints.gesture.name.unique",
 * )
 * @Vich\Uploadable
 * @PublishableGesture()
 */
class Gesture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list","minimal"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Groups({"list","show","minimal"})
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
     * @Vich\UploadableField(mapping="gesture_profileVideo",fileNameProperty="profileVideo")
     *
     * @Assert\File(
     *     maxSize="1M",
     *     maxSizeMessage="admin.constraints.gesture.video.too_heavy",
     *     mimeTypes={
     *          "video/mp4"
     *     },
     *     mimeTypesMessage="admin.constraints.gesture.video.type",
     *     disallowEmptyMessage="admin.constraints.gesture.video.empty",
     *     notFoundMessage="admin.constraints.gesture.video.not_found",
     *     notReadableMessage="admin.constraints.gesture.video.not_readable",
     *     uploadErrorMessage="admin.constraints.gesture.video.error"
     * )
     */
    private $profileVideoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show"})
     */
    private $video;

    /**
     * @Vich\UploadableField(mapping="gesture_video", fileNameProperty="video")
     *
     * @Assert\File(
     *     maxSize="1M",
     *     maxSizeMessage="admin.constraints.gesture.video.too_heavy",
     *     mimeTypes={
     *          "video/mp4"
     *     },
     *     mimeTypesMessage="admin.constraints.gesture.video.type",
     *     disallowEmptyMessage="admin.constraints.gesture.video.empty",
     *     notFoundMessage="admin.constraints.gesture.video.not_found",
     *     notReadableMessage="admin.constraints.gesture.video.not_readable",
     *     uploadErrorMessage="admin.constraints.gesture.video.error"
     * )
     */
    private $videoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list","show"})
     */
    private $cover;

    /**
     * @Vich\UploadableField(mapping="gesture_cover", fileNameProperty="cover")
     *
     * @Assert\Image(
     *     maxSize="1M",
     *     maxSizeMessage="admin.constraints.gesture.cover.too_heavy",
     *     mimeTypes={
     *          "image/jpeg",
     *          "image/jpg",
     *     },
     *     mimeTypesMessage="admin.constraints.gesture.cover.type",
     *     minWidth="350",
     *     minHeight="350",
     *     sizeNotDetectedMessage="admin.constraints.gesture.cover.sizeNotDetected",
     *     minWidthMessage="admin.constraints.gesture.cover.shortWidth",
     *     minHeightMessage="admin.constraints.gesture.cover.shortHeight",
     * )
     *
     * @var File
     *
     */
    private $coverFile;

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

    /**
     * @ORM\Column(type="datetime",nullable=true)
     *
     */
    private $updatedAt;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id){
        $this->id = $id;

        return $this;
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
    public function setCreationDateValue()
    {
        $this->creationDate = new \DateTime("now",new \DateTimeZone("Europe/Brussels"));
    }

    /**
     * Triggered on insert
     * @ORM\PrePersist
     */
    public function setPublicationDateValue(){
        if($this->getIsPublished()){
            $this->setPublicationDate(new \DateTime("now",new \DateTimeZone("Europe/Brussels")));
        }
    }
    /**
     * Triggerd on update
     * @ORM\PreUpdate
     */
    public function setPublicationDateValueIfPublished(){
        if($this->getIsPublished() && empty($this->getPublicationDate())){
            $this->setPublicationDate(new \DateTime("now",new \DateTimeZone("Europe/Brussels")));
        }else if(!$this->getIsPublished() && !empty($this->getPublicationDate())){
            $this->setPublicationDate(null);
        }
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return File|null
     */
    public function getCoverFile(): ?File
    {
        return $this->coverFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $coverFile
     */
    public function setCoverFile(?File $coverFile = null): void
    {
        $this->coverFile = $coverFile;

        if (null !== $coverFile) {
            $this->updatedAt = new \DateTime("now",new \DateTimeZone("Europe/Brussels"));
        }
    }

    /**
     * @return mixed
     */
    public function getProfileVideoFile()
    {
        return $this->profileVideoFile;
    }

    /**
     * @param mixed $profileVideoFile
     */
    public function setProfileVideoFile($profileVideoFile)
    {
        $this->profileVideoFile = $profileVideoFile;

        if (null !== $profileVideoFile) {
            $this->updatedAt = new \DateTime("now",new \DateTimeZone("Europe/Brussels"));
        }
    }

    /**
     * @return mixed
     */
    public function getVideoFile()
    {
        return $this->videoFile;
    }

    /**
     * @param mixed $videoFile
     */
    public function setVideoFile($videoFile)
    {
        $this->videoFile = $videoFile;

        if (null !== $videoFile) {
            $this->updatedAt = new \DateTime("now",new \DateTimeZone("Europe/Brussels"));
        }
    }

}
