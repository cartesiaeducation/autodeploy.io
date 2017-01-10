<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Log.
 *
 * @ORM\Table(name="app_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LogRepository")
 */
class Log
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
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="consumer_name", type="string", length=255)
     */
    private $consumerName;

    /**
     * @var string
     *
     * @ORM\Column(name="consumer_params", type="text")
     */
    private $consumerParams;

    /**
     * @var array
     *
     * @ORM\Column(name="consumer_options", type="array")
     */
    private $consumerOptions;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_success", type="boolean")
     */
    private $isSuccess;

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
     * Set message.
     *
     * @param string $message
     *
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set consumerName.
     *
     * @param string $consumerName
     *
     * @return Log
     */
    public function setConsumerName($consumerName)
    {
        $this->consumerName = $consumerName;

        return $this;
    }

    /**
     * Get consumerName.
     *
     * @return string
     */
    public function getConsumerName()
    {
        return $this->consumerName;
    }

    /**
     * Set consumerParams.
     *
     * @param string $consumerParams
     *
     * @return Log
     */
    public function setConsumerParams($consumerParams)
    {
        $this->consumerParams = $consumerParams;

        return $this;
    }

    /**
     * Get consumerParams.
     *
     * @return string
     */
    public function getConsumerParams()
    {
        return $this->consumerParams;
    }

    /**
     * Set consumerOptions.
     *
     * @param array $consumerOptions
     *
     * @return Log
     */
    public function setConsumerOptions($consumerOptions)
    {
        $this->consumerOptions = $consumerOptions;

        return $this;
    }

    /**
     * Get consumerOptions.
     *
     * @return array
     */
    public function getConsumerOptions()
    {
        return $this->consumerOptions;
    }

    /**
     * Set isSuccess.
     *
     * @param bool $isSuccess
     *
     * @return Log
     */
    public function setIsSuccess($isSuccess)
    {
        $this->isSuccess = $isSuccess;

        return $this;
    }

    /**
     * Get isSuccess.
     *
     * @return bool
     */
    public function getIsSuccess()
    {
        return $this->isSuccess;
    }
}
