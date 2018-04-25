<?php
namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User extends BaseUser
{
    /**
     * @var integer
     * 
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank(message="fos_user.firstname.blank",
     * groups={"Registration"})
     * 
     * @Assert\Length(
     *  min=3,
     *  max=255,
     *  minMessage="fos_user.firstname.short",
     *  maxMessage="fos_user.firstname.long",
     *  groups={"Registration"}
     * )
     * 
     * @Assert\Type(type="string",
     *  message="fos_user.firstname.invalid")
     * 
     * @Assert\Regex(pattern="/\d/",
     *  match=false,
     *  message="fos_user.firstname.invalid_type")
     */
    protected $firstname;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * 
     *  @Assert\NotBlank(message="fos_user.lastname.blank", groups={"Registration"})
     * @Assert\Length(
     *     min=3,
     *     max=255,
     *     minMessage="fos_user.lastname.short",
     *     maxMessage="fos_user.lastname.long",
     *     groups={"Registration"}
     * )
     * 
     * @Assert\Type(type="string",
     *  message="fos_user.lastname.invalid")
     * 
     *  @Assert\Regex(pattern="/\d/",
     *  match=false,
     *  message="fos_user.lastname.invalid_type")
     */
    protected $lastname;

    public function __construct()
    {
        parent::__construct();
        // your own logic
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
}
