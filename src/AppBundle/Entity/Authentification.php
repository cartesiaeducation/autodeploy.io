<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Authentification.
 *
 * @ORM\Table(name="app_project_authentification")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthentificationRepository")
 */
class Authentification
{
    use TimestampableEntity;

    const TYPE_REPOSITORY = 'repository';
    const TYPE_SERVER     = 'server';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="#-----BEGIN .+ PRIVATE KEY-----#",
     *     match=true,
     *     message="You need to enter a valid SSH private key"
     * )
     *
     * @ORM\Column(name="ssh", type="text")
     */
    private $ssh;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="#ssh-rsa AAAA[0-9A-Za-z+/]+[=]{0,3} ([^@]+@[^@]+)#",
     *     match=true,
     *     message="You need to enter a valid SSH public key"
     * )
     *
     * @ORM\Column(name="ssh_public", type="text")
     */
    private $sshPublic;

    /**
     * @var \AppBundle\Entity\Project
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;

    /**
     * @var \AppBundle\Entity\Environment
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Environment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="environment_id", referencedColumnName="id")
     * })
     */
    private $environment;

    /**
     * @var \AppBundle\Entity\User
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_valid", type="boolean")
     */
    private $isValid;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_checked", type="boolean")
     */
    private $isChecked;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->isValid   = false;
        $this->isChecked = false;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Generate default name for the key.
     */
    public function generateDefaultName()
    {
        $this->setName('Deploy Key');
    }

    /**
     * Set ssh.
     *
     * @param string $ssh
     *
     * @return Authentification
     */
    public function setSsh($ssh)
    {
        $this->ssh = $ssh;

        return $this;
    }

    /**
     * Get ssh.
     *
     * @return string
     */
    public function getSsh()
    {
        return $this->ssh;
    }

    /**
     * @return string
     */
    public function getSshPublic()
    {
        return $this->sshPublic;
    }

    /**
     * @param string $sshPublic
     *
     * @return Authentification
     */
    public function setSshPublic($sshPublic)
    {
        $this->sshPublic = $sshPublic;

        return $this;
    }

    /**
     * Set project.
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Authentification
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project.
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set isValid.
     *
     * @param bool $isValid
     *
     * @return Authentification
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get isValid.
     *
     * @return bool
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Authentification
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Authentification
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isChecked.
     *
     * @param bool $isChecked
     *
     * @return Authentification
     */
    public function setIsChecked($isChecked)
    {
        $this->isChecked = $isChecked;

        return $this;
    }

    /**
     * Get isChecked.
     *
     * @return bool
     */
    public function getIsChecked()
    {
        return $this->isChecked;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Authentification
     */
    public function setType($type)
    {
        $this->type = $type;

        if (!$this->getName()) {
            $this->generateDefaultName();
        }

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getTypeChoices()
    {
        return [
            self::TYPE_REPOSITORY => 'GIT Repository',
            self::TYPE_SERVER     => 'Server',
        ];
    }

    /**
     * @return bool
     */
    public function isRepository()
    {
        return $this->getType() === self::TYPE_REPOSITORY;
    }

    /**
     * @return bool
     */
    public function isServer()
    {
        return $this->getType() === self::TYPE_SERVER;
    }

    /**
     * Set environment.
     *
     * @param Environment $environment
     *
     * @return Authentification
     */
    public function setEnvironment(Environment $environment = null)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Get environment.
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}
