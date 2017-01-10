<?php

namespace AppBundle\Form\Model\Capistrano;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Files.
 */
class Files
{
    /**
     * @var array
     *
     * @Assert\NotNull()
     */
    public $linkedFiles;

    /**
     * @var array
     *
     * @Assert\NotNull()
     */
    public $linkedDirs;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->linkedFiles = [];
        $this->linkedDirs  = [];
    }
}
