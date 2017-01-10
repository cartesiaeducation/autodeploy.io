<?php

namespace AppBundle\Services\Authentification;

use AppBundle\Manager\ProjectAuthentificationManager;
use AppBundle\Manager\TaskManager;
use AppBundle\Services\Git\GitManager;
use AppBundle\Services\SSH\SshManager;

/**
 * Class AuthentificationChecker.
 */
class AuthentificationChecker
{
    /**
     * @var ProjectAuthentificationManager
     */
    private $authentificationManager;

    /**
     * @var GitManager
     */
    private $gitManager;

    /**
     * @var SshManager
     */
    private $sshManager;

    /**
     * @var TaskManager
     */
    private $taskManager;

    /**
     * @param ProjectAuthentificationManager $authentificationManager
     * @param GitManager                     $gitManager
     * @param SshManager                     $sshManager
     * @param TaskManager                    $taskManager
     */
    public function __construct(ProjectAuthentificationManager $authentificationManager, GitManager $gitManager, SshManager $sshManager, TaskManager $taskManager)
    {
        $this->authentificationManager = $authentificationManager;
        $this->gitManager              = $gitManager;
        $this->sshManager              = $sshManager;
        $this->taskManager             = $taskManager;
    }

    /**
     * @param $authentificationId
     *
     * @return bool
     */
    public function check($authentificationId)
    {
        $authentification = $this->authentificationManager->findOneById($authentificationId);
        if (!$authentification) {
            return false;
        }

        $isValid = false;

        if ($authentification->isRepository()) {
            $isValid = $this->gitManager->checkAuthentification($authentification);
        } elseif ($authentification->isServer()) {
            $isValid = $this->sshManager->checkAuthentification($authentification);
        }

        if ($isValid) {
            $this->authentificationManager->setIsValid($authentification);
            if (!$authentification->getProject()->hasTasks()) {
                $this->taskManager->sendRetrieveList($authentification->getProject());
            }
        } else {
            $this->authentificationManager->setIsInvalid($authentification);
        }
    }
}
