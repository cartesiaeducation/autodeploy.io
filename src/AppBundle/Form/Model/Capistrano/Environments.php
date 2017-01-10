<?php

namespace AppBundle\Form\Model\Capistrano;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Environments.
 */
class Environments
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $env;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $branch;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $deployTo;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $tmp;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $logLevel;

    /**
     * @var array
     *
     * @Assert\NotNull()
     * @Assert\Count(
     *      min = "1",
     *      minMessage = "You must specify at least one server per environment"
     * )
     */
    public $servers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->branch   = 'master';
        $this->deployTo = '/var/www';
        $this->tmp      = '{ "#{fetch(:deploy_to)}/tmp" }';
        $this->logLevel = ':debug';

        $this->servers = new ArrayCollection();
        $server        = new Server();
        $this->servers->add($server);
    }
}
