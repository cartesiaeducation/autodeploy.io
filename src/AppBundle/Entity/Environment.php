<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Environment.
 *
 * @ORM\Table(name="app_project_environment",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="project_env_unique_idx", columns={"project_id", "name"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EnvironmentRepository")
 *
 * @UniqueEntity({"project", "name"})
 */
class Environment
{
    use TimestampableEntity;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Authentification", mappedBy="environment", cascade={"all"})
     * )
     */
    private $authentifications;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Queue", mappedBy="environment", cascade={"all"})
     * )
     */
    private $queues;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->authentifications = new ArrayCollection();
        $this->queues            = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Environment
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
     * Set project.
     *
     * @param Project $project
     *
     * @return Environment
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project.
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add authentification.
     *
     * @param Authentification $authentification
     *
     * @return Environment
     */
    public function addAuthentification(Authentification $authentification)
    {
        $this->authentifications[] = $authentification;

        return $this;
    }

    /**
     * Remove authentification.
     *
     * @param Authentification $authentification
     */
    public function removeAuthentification(Authentification $authentification)
    {
        $this->authentifications->removeElement($authentification);
    }

    /**
     * Get authentifications.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthentifications()
    {
        return $this->authentifications;
    }

    /**
     * @return Authentification|null
     */
    public function getAuthentification()
    {
        return $this->getAuthentifications()->count() ? $this->getAuthentifications()->last() : null;
    }

    /**
     * Add queue.
     *
     * @param \AppBundle\Entity\Queue $queue
     *
     * @return Environment
     */
    public function addQueue(Queue $queue)
    {
        $this->queues[] = $queue;

        return $this;
    }

    /**
     * Remove queue.
     *
     * @param \AppBundle\Entity\Queue $queue
     */
    public function removeQueue(Queue $queue)
    {
        $this->queues->removeElement($queue);
    }

    /**
     * Get queues.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s', $this->getName());
    }
}
