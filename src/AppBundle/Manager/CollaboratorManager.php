<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Collaborator;
use AppBundle\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CollaboratorManager.
 */
class CollaboratorManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Project $project
     *
     * @return Collaborator
     */
    public function create(Project $project)
    {
        $collaborator = new Collaborator();
        $collaborator->setProject($project);

        return $collaborator;
    }

    /**
     * @param Collaborator $collaborator
     */
    public function save(Collaborator $collaborator)
    {
        if (null === $collaborator->getId()) {
            $this->em->persist($collaborator);
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
     * @return \AppBundle\Repository\CollaboratorRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('AppBundle:Collaborator');
    }
}
