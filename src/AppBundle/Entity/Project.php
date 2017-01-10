<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project.
 *
 * @ORM\Table(name="app_project",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="repository_unique_idx", columns={"repository"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 *
 * @UniqueEntity({"repository"})
 */
class Project
{
    use TimestampableEntity;

    const TYPE_GIT = 'GIT';

    const TASK_STATUS_WAITING           = 'waiting';
    const TASK_STATUS_QUEUE             = 'queue';
    const TASK_STATUS_SUCCESS           = 'success';
    const TASK_STATUS_PROGRESS          = 'progress';
    const TASK_STATUS_ERROR             = 'error';
    const TASK_STATUS_ERROR_MISSING_CAP = 'error_missing_cap';
    const TASK_STATUS_ERROR_AUTH        = 'error_auth';

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
     * @ORM\Column(name="branch", type="string", length=255)
     */
    private $branch;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     *     pattern="/(git@.+|https:\/\/)/",
     *     match=true,
     *     message="Your repository must start by git@ or https://"
     * )
     *
     * @ORM\Column(name="repository", type="string", length=255)
     */
    private $repository;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Authentification", mappedBy="project", cascade={"all"})
     * )
     */
    private $authentifications;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Task", mappedBy="project", cascade={"all"})
     * )
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Collaborator", mappedBy="project", cascade={"all"})
     * )
     */
    private $collaborators;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Slack", mappedBy="project", cascade={"all"})
     * )
     */
    private $slacks;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Webhook", mappedBy="project", cascade={"all"})
     * )
     */
    private $webhooks;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Queue", mappedBy="project", cascade={"all"})
     * )
     */
    private $queues;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Environment", mappedBy="project", cascade={"all"})
     * )
     */
    private $environments;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    private $isEnabled;

    /**
     * @var string
     *
     * @ORM\Column(name="task_retrieve_status", type="string", length=255)
     */
    private $taskRetrieveStatus;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->isEnabled          = true;
        $this->authentifications  = new ArrayCollection();
        $this->tasks              = new ArrayCollection();
        $this->collaborators      = new ArrayCollection();
        $this->environments       = new ArrayCollection();
        $this->webhooks           = new ArrayCollection();
        $this->slacks             = new ArrayCollection();
        $this->queues             = new ArrayCollection();
        $this->branch             = 'master';
        $this->taskRetrieveStatus = self::TASK_STATUS_WAITING;
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
     * @return Project
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
     * Set description.
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     *
     * @return Project
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Project
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     *
     * @return Project
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Project
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Add authentification.
     *
     * @param Authentification $authentification
     *
     * @return Project
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
     * Return TRUE if the project has a valid authentification.
     *
     * @return bool
     */
    public function hasAuthentification()
    {
        $hasRepository = $this->getAuthentifications()->filter(function (Authentification $authentification) {
            return $authentification->isRepository();
        });

        $hasServer = $this->getAuthentifications()->filter(function (Authentification $authentification) {
            return $authentification->isServer();
        });

        return $hasRepository->count() && $hasServer->count();
    }

    /**
     * Return TRUE if the project has a valid authentification.
     *
     * @return bool
     */
    public function hasAuthentificationServer()
    {
        $hasServer = $this->getAuthentifications()->filter(function (Authentification $authentification) {
            return $authentification->isServer();
        });

        return (bool) $hasServer->count();
    }

    /**
     * Return TRUE if the project has a valid authentification.
     *
     * @return bool
     */
    public function hasAuthentificationRepository()
    {
        $hasRepository = $this->getAuthentifications()->filter(function (Authentification $authentification) {
            return $authentification->isRepository();
        });

        return (bool) $hasRepository->count();
    }

    /**
     * Return TRUE if the project has a valid authentification.
     *
     * @return bool
     */
    public function hasValidAuthentificationRepository()
    {
        $hasRepository = $this->getAuthentifications()->filter(function (Authentification $authentification) {
            return $authentification->getIsValid() && $authentification->isRepository() && $authentification->getIsChecked();
        });

        return $hasRepository->count() > 0;
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
    public function getAuthentificationRepository()
    {
        $repository = $this->getAuthentifications()->filter(function (Authentification $authentification) {
            return $authentification->isRepository();
        });

        return $repository->count() ? $repository->last() : null;
    }

    /**
     * @return array
     */
    public function getAuthentificationsRepository()
    {
        return $this->getAuthentifications()->filter(function (Authentification $authentification) {
            return $authentification->isRepository();
        });
    }

    /**
     * Get types availables.
     *
     * @return array
     */
    public function getTypes()
    {
        return [
            self::TYPE_GIT => 'GIT',
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s', $this->getName());
    }

    /**
     * Add task.
     *
     * @param \AppBundle\Entity\Task $task
     *
     * @return Project
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task.
     *
     * @param \AppBundle\Entity\Task $task
     */
    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @return bool
     */
    public function hasTasks()
    {
        return $this->getTasks()->filter(function (Task $task) {
            return $task->getIsEnabled();
        })->count() > 0;
    }

    /**
     * Add collaborator.
     *
     * @param Collaborator $collaborator
     *
     * @return Project
     */
    public function addCollaborator(Collaborator $collaborator)
    {
        $this->collaborators[] = $collaborator;

        return $this;
    }

    /**
     * Remove collaborator.
     *
     * @param Collaborator $collaborator
     */
    public function removeCollaborator(Collaborator $collaborator)
    {
        $this->collaborators->removeElement($collaborator);
    }

    /**
     * Get collaborators.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Set branch.
     *
     * @param string $branch
     *
     * @return Project
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch.
     *
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Add slack.
     *
     * @param \AppBundle\Entity\Slack $slack
     *
     * @return Project
     */
    public function addSlack(Slack $slack)
    {
        $this->slacks[] = $slack;

        return $this;
    }

    /**
     * Remove slack.
     *
     * @param \AppBundle\Entity\Slack $slack
     */
    public function removeSlack(Slack $slack)
    {
        $this->slacks->removeElement($slack);
    }

    /**
     * Get slacks.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSlacks()
    {
        return $this->slacks;
    }

    /**
     * Add webhook.
     *
     * @param \AppBundle\Entity\Webhook $webhook
     *
     * @return Project
     */
    public function addWebhook(Webhook $webhook)
    {
        $this->webhooks[] = $webhook;

        return $this;
    }

    /**
     * Remove webhook.
     *
     * @param \AppBundle\Entity\Webhook $webhook
     */
    public function removeWebhook(Webhook $webhook)
    {
        $this->webhooks->removeElement($webhook);
    }

    /**
     * Get webhooks.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWebhooks()
    {
        return $this->webhooks;
    }

    /**
     * Add queue.
     *
     * @param \AppBundle\Entity\Queue $queue
     *
     * @return Project
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
     * Add environment.
     *
     * @param \AppBundle\Entity\Environment $environment
     *
     * @return Project
     */
    public function addEnvironment(Environment $environment)
    {
        $this->environments[] = $environment;

        return $this;
    }

    /**
     * Remove environment.
     *
     * @param \AppBundle\Entity\Environment $environment
     */
    public function removeEnvironment(Environment $environment)
    {
        $this->environments->removeElement($environment);
    }

    /**
     * Get environments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    /**
     * @return bool
     */
    public function hasTaskDeploy()
    {
        return (bool) $this->tasks->filter(function (Task $task) {
            return $task->getName() === 'deploy';
        })->count();
    }

    /**
     * @return Task
     */
    public function getTaskDeploy()
    {
        return $this->tasks->filter(function (Task $task) {
            return $task->getName() === 'deploy';
        })->first();
    }

    /**
     * Set taskRetrieveStatus.
     *
     * @param string $taskRetrieveStatus
     *
     * @return Project
     */
    public function setTaskRetrieveStatus($taskRetrieveStatus)
    {
        $this->taskRetrieveStatus = $taskRetrieveStatus;

        return $this;
    }

    /**
     * Get taskRetrieveStatus.
     *
     * @return string
     */
    public function getTaskRetrieveStatus()
    {
        return $this->taskRetrieveStatus;
    }

    /**
     * @return bool
     */
    public function isTaskRetrieveInProgress()
    {
        return $this->getTaskRetrieveStatus() === self::TASK_STATUS_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isTaskRetrieveQueue()
    {
        return $this->getTaskRetrieveStatus() === self::TASK_STATUS_QUEUE;
    }

    /**
     * @return bool
     */
    public function isTaskRetrieveSuccess()
    {
        return $this->getTaskRetrieveStatus() === self::TASK_STATUS_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isTaskRetrieveWaiting()
    {
        return $this->getTaskRetrieveStatus() === self::TASK_STATUS_WAITING;
    }

    /**
     * @return bool
     */
    public function isTaskRetrieveErrorMissingCapFile()
    {
        return $this->getTaskRetrieveStatus() === self::TASK_STATUS_ERROR_MISSING_CAP;
    }

    /**
     * @return bool
     */
    public function isTaskRetrieveErrorAuth()
    {
        return $this->getTaskRetrieveStatus() === self::TASK_STATUS_ERROR_AUTH;
    }

    /**
     * @return bool
     */
    public function isTaskRetrieveError()
    {
        return in_array($this->getTaskRetrieveStatus(), [
            self::TASK_STATUS_ERROR,
            self::TASK_STATUS_ERROR_MISSING_CAP,
            self::TASK_STATUS_ERROR_AUTH,
        ]);
    }

    /**
     * Disable all tasks.
     */
    public function disableTasks()
    {
        foreach ($this->getTasks() as $task) {
            $task->setIsEnabled(false);
        }
    }
}
