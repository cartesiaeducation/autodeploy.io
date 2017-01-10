<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Authentification;
use AppBundle\Entity\Environment;
use AppBundle\Entity\Project;
use AppBundle\Services\SSH\SshManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EnvironmentManager.
 */
class EnvironmentManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SshManager
     */
    private $sshManager;

    /**
     * @var ProjectAuthentificationManager
     */
    private $authentificationManager;

    /**
     * @param EntityManagerInterface         $em
     * @param SshManager                     $sshManager
     * @param ProjectAuthentificationManager $authentificationManager
     */
    public function __construct(EntityManagerInterface $em, SshManager $sshManager, ProjectAuthentificationManager $authentificationManager)
    {
        $this->em                      = $em;
        $this->sshManager              = $sshManager;
        $this->authentificationManager = $authentificationManager;
    }

    /**
     * @param Environment $environment
     */
    public function save(Environment $environment)
    {
        if (null === $environment->getId()) {
            $environment->addAuthentification($this->createDefaultAuthentification($environment));
            $this->em->persist($environment);
        }

        $this->em->flush();
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    public function getByProject(Project $project)
    {
        return $this->getRepository()->findByProject($project->getId());
    }

    /**
     * @return \AppBundle\Repository\EnvironmentRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('AppBundle:Environment');
    }

    /**
     * @param Project $project
     *
     * @return Environment
     */
    public function create(Project $project)
    {
        $environment = new Environment();
        $environment->setProject($project);

        return $environment;
    }

    /**
     * @param Environment $environment
     */
    public function delete(Environment $environment)
    {
        $this->em->remove($environment);
        $this->em->flush();
    }

    /**
     * @param Environment $environment
     *
     * @return Authentification
     */
    public function createDefaultAuthentification(Environment $environment)
    {
        $randomKey = $this->sshManager->generateKey();

        $authentification = $this->authentificationManager->create($environment->getProject(), $environment->getProject()->getUser(), Authentification::TYPE_SERVER);
        $authentification->setSsh($randomKey['privatekey']);
        $authentification->setSshPublic($randomKey['publickey']);
        $authentification->setProject($environment->getProject());
        $authentification->setEnvironment($environment);

        return $authentification;
    }
}
