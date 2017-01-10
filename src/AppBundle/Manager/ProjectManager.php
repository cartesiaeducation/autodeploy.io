<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Authentification;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Services\SSH\SshManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ProjectManager.
 */
class ProjectManager
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
     * Get repository.
     *
     * @return \AppBundle\Repository\ProjectRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('AppBundle:Project');
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function findAll(User $user)
    {
        return $this->getRepository()->getByUser($user);
    }

    /**
     * @param $id
     *
     * @return Project
     */
    public function findOneById($id)
    {
        return $this->getRepository()->findOneById($id);
    }

    /**
     * @param Project $project
     */
    public function save(Project $project)
    {
        if (null === $project->getId()) {
            $this->em->persist($project);
        }

        if (!$project->hasAuthentificationRepository()) {
            $project->addAuthentification($this->createDefaultAuthentificationRepository($project));
        }

        $this->em->flush();
    }

    /**
     * @return Project
     */
    public function create(User $user)
    {
        $project = new Project();
        $project->setType(Project::TYPE_GIT);
        $project->setUser($user);

        return $project;
    }

    /**
     * @param Project $project
     *
     * @return Authentification
     */
    public function createDefaultAuthentificationRepository(Project $project)
    {
        $randomKey = $this->sshManager->generateKey();

        $authentification = $this->authentificationManager->create($project, $project->getUser(), Authentification::TYPE_REPOSITORY);
        $authentification->setSsh($randomKey['privatekey']);
        $authentification->setSshPublic($randomKey['publickey']);
        $authentification->setProject($project);

        return $authentification;
    }

    /**
     * @param Project $project
     */
    public function checkPermissions(Project $project)
    {
        if ($project->hasAuthentificationRepository()) {
            $this->authentificationManager->checkAuthentification($project->getAuthentificationRepository());
        }
    }

    /**
     * @param Project $project
     */
    public function delete(Project $project)
    {
        $this->em->remove($project);
        $this->em->flush();
    }
}
