<?php

namespace AppBundle\Form\Model\Capistrano;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Server.
 */
class Server
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $host;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $user;
    /**
     * @var int
     *
     * @Assert\NotBlank()
     */
    public $port;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 22;
        $this->user = 'www-data';
    }
}
