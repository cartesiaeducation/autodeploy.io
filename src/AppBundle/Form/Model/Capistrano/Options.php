<?php

namespace AppBundle\Form\Model\Capistrano;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Options.
 */
class Options
{
    /**
     * @var bool
     *
     * @Assert\NotNull()
     */
    public $generateGemfile;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->generateGemfile = true;
    }
}
