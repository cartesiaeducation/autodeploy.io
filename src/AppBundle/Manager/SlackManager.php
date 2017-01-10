<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Project;
use AppBundle\Entity\Slack;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SlackManager.
 */
class SlackManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Slack $slack
     */
    public function save(Slack $slack)
    {
        if (null === $slack->getId()) {
            $this->em->persist($slack);
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
     * @return \AppBundle\Repository\SlackRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('AppBundle:Slack');
    }

    /**
     * @param Project $project
     *
     * @return Slack
     */
    public function create(Project $project)
    {
        $slack = new Slack();
        $slack->setProject($project);
        $slack->setIsEnabled(true);

        return $slack;
    }
}
