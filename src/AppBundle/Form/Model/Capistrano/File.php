<?php

namespace AppBundle\Form\Model\Capistrano;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class File.
 */
class File
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $path;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->path;
    }
}
