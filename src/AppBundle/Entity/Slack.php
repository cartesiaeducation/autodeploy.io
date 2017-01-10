<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Slack.
 *
 * @ORM\Table(name="app_project_slack", uniqueConstraints={
 *         @ORM\UniqueConstraint(name="project_slack_unique_idx", columns={"project_id", "webhook_url"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SlackRepository")
 *
 * @UniqueEntity({"project", "webhookUrl"})
 */
class Slack
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
     * @Assert\Url()
     *
     * @ORM\Column(name="webhook_url", type="string", length=255)
     */
    private $webhookUrl;

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
     * @var bool
     *
     * @Assert\NotNull()
     *
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    private $isEnabled;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->isEnabled = true;
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
     * Set webhookUrl.
     *
     * @param string $webhookUrl
     *
     * @return Slack
     */
    public function setWebhookUrl($webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;

        return $this;
    }

    /**
     * Get webhookUrl.
     *
     * @return string
     */
    public function getWebhookUrl()
    {
        return $this->webhookUrl;
    }

    /**
     * Set project.
     *
     * @param Project $project
     *
     * @return Slack
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
     * Set isEnabled.
     *
     * @param bool $isEnabled
     *
     * @return Slack
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled.
     *
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }
}
