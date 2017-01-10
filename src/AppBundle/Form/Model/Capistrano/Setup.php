<?php

namespace AppBundle\Form\Model\Capistrano;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Setup.
 */
class Setup
{
    /**
     * @var int
     *
     * @Assert\NotBlank()
     */
    public $keepReleases;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $directory;

    /**
     * @var array
     */
    public $plugins;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->keepReleases = 5;
        $this->directory    = 'deploy/';
        $this->plugins      = [];
    }
}
