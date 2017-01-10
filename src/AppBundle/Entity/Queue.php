<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Queue.
 *
 * @ORM\Table(name="app_project_queue")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QueueRepository")
 *
 * @UniqueEntity(fields={"project", "task", "environment"}, repositoryMethod="isTaskExisting", message="A task with the same name is already in Queue for the same Environment")
 */
class Queue
{
    const STATE_QUEUE       = 'queue';
    const STATE_IN_PROGRESS = 'progress';
    const STATE_SUCCESS     = 'success';
    const STATE_ERROR       = 'error';

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
     * @var \AppBundle\Entity\Task
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Task")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     * })
     */
    private $task;

    /**
     * @var \AppBundle\Entity\Environment
     *
     * @Assert\NotNull()
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
     * @ORM\Column(name="is_processed", type="boolean")
     */
    private $isProcessed;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * @var bool
     *
     * @Assert\NotNull()
     *
     * @ORM\Column(name="is_send_to_queue", type="boolean")
     */
    private $sendToQueue;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="text", nullable=true)
     */
    private $output;

    /**
     * @var string
     *
     * @ORM\Column(name="output_error", type="text", nullable=true)
     */
    private $errorOutput;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="branch", type="string", length=255)
     */
    private $branch;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->isProcessed = false;
        $this->sendToQueue = false;
        $this->state       = self::STATE_QUEUE;
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
     * Set isProcessed.
     *
     * @param bool $isProcessed
     *
     * @return Queue
     */
    public function setIsProcessed($isProcessed)
    {
        $this->isProcessed = $isProcessed;

        return $this;
    }

    /**
     * Get isProcessed.
     *
     * @return bool
     */
    public function getIsProcessed()
    {
        return $this->isProcessed;
    }

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return Queue
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set task.
     *
     * @param Task $task
     *
     * @return Queue
     */
    public function setTask(Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task.
     *
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Queue
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
     * Set project.
     *
     * @param Project $project
     *
     * @return Queue
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
     * Set environment.
     *
     * @param Environment $environment
     *
     * @return Queue
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
     * Set sendToQueue.
     *
     * @param bool $sendToQueue
     *
     * @return Queue
     */
    public function setSendToQueue($sendToQueue)
    {
        $this->sendToQueue = $sendToQueue;

        return $this;
    }

    /**
     * Get sendToQueue.
     *
     * @return bool
     */
    public function getSendToQueue()
    {
        return $this->sendToQueue;
    }

    /**
     * Set output.
     *
     * @param string $output
     *
     * @return Queue
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get output.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set errorOutput.
     *
     * @param string $errorOutput
     *
     * @return Queue
     */
    public function setErrorOutput($errorOutput)
    {
        $this->errorOutput = $errorOutput;

        return $this;
    }

    /**
     * Get errorOutput.
     *
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->errorOutput;
    }

    /**
     * @return string|null
     */
    public function getOutputFormatted()
    {
        $output = trim($this->getOutput());
        if (false !== $pos = mb_strpos($output, 'ssh-add')) {
            $output = mb_substr($output, $pos);
        }

        $lines = explode("\n", $output);
        if (!count($lines)) {
            return;
        }

        $formatted = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)
                && false === mb_strpos($line, 'ssh-add ')
                && false === mb_strpos($line, 'Identity added')) {
                $formatted .= $line . "\n";
            }
        }

        return trim($formatted);
    }

    /**
     * @return string|null
     */
    public function getErrorOutputFormatted()
    {
        $output = trim($this->getErrorOutput());
        if (false !== $pos = mb_strpos($output, 'ssh-add')) {
            $output = mb_substr($output, $pos);
        }

        $lines = explode("\n", $output);
        if (!count($lines)) {
            return;
        }

        $formatted = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)
                && false === mb_strpos($line, 'ssh-add ')
                && false === mb_strpos($line, 'Identity added')) {
                $formatted .= $line . "\n";
            }
        }

        return trim($formatted);
    }

    /**
     * Set branch.
     *
     * @param string $branch
     *
     * @return Queue
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
}
