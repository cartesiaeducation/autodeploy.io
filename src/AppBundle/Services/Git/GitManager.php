<?php

namespace AppBundle\Services\Git;

use AppBundle\Entity\Authentification;
use AppBundle\Entity\Project;
use AppBundle\Util\ProcessManager;

/**
 * Class GitManager.
 */
class GitManager
{
    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var ProcessManager
     */
    private $processManager;

    /**
     * @param string         $cachePath
     * @param ProcessManager $processManager
     */
    public function __construct($cachePath, ProcessManager $processManager)
    {
        $this->cachePath      = $cachePath;
        $this->processManager = $processManager;
    }

    /**
     * @param Authentification $authentification
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkAuthentification(Authentification $authentification)
    {
        try {
            $this->processManager->execute(sprintf('git ls-remote "%s"', $authentification->getProject()->getRepository()), $authentification, 45);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param Project $project
     * @param string  $branch
     *
     * @return string|null
     */
    public function getLastCommit(Project $project, $branch = 'master')
    {
        try {
            $output = $this->processManager->execute(sprintf('git ls-remote "%s" "%s"', $project->getRepository(), $branch), $project->getAuthentificationRepository(), 45);
            $output = explode("\t", $output);

            return $output[0];
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * @param Project $project
     * @param string  $branch
     *
     * @throws \Exception
     */
    public function cloneRepository(Project $project, $branch = 'master')
    {
        if (!$project->getAuthentificationRepository()) {
            throw new \Exception('Need one authentification for the GIT repository');
        }

        if (!$this->checkAuthentification($project->getAuthentificationRepository())) {
            throw new \Exception('Cant authenticate on GIT repository');
        }

        if (!$lastCommit = $this->getLastCommit($project, $branch)) {
            throw new \Exception('Cant retrieve last commit');
        }

        $localPath = sprintf('%s/%s', $this->cachePath, $project->getId());
        if (!file_exists($localPath)) {
            $this->processManager->execute(sprintf('git clone -q "%s" %s && cd "%s" && git checkout -q -b deploy "%s"',
                $project->getRepository(),
                $localPath,
                $localPath,
                $lastCommit
            ), $project->getAuthentificationRepository());
        } else {
            $this->processManager->execute(sprintf('cd "%s" && git fetch -q origin && git fetch --tags -q origin && git reset -q --hard "%s" && git clean -q -d -x -f',
                $localPath,
                $lastCommit
            ), $project->getAuthentificationRepository());
        }

        return $localPath;
    }
}
