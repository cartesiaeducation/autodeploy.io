<?php

namespace AppBundle\Form\Model\Capistrano;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Wizard.
 */
class Wizard
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $repositoryUrl;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $repositoryTree;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $scm;

    /**
     * @var array
     *
     * @Assert\NotNull()
     */
    public $setup;

    /**
     * @var array
     *
     * @Assert\NotNull()
     * @Assert\Count(
     *      min = "1",
     *      minMessage = "You must specify at least one environment"
     * )
     */
    public $environments;

    /**
     * @var array
     */
    public $files;

    /**
     * @var array
     *
     * @Assert\NotNull()
     */
    public $options;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setup          = new Setup();
        $this->files          = new Files();
        $this->options        = new Options();
        $this->repositoryTree = '{ "#{fetch(:deploy_to)}/repo" }';
        $this->environments   = new ArrayCollection();

        $env = new Environments();
        $this->environments->add($env);
    }
}
