<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Authentification;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class ProjectAuthentificationManager.
 */
class ProjectAuthentificationManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Producer
     */
    private $producer;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     * @param Producer               $producer
     */
    public function __construct(EntityManagerInterface $em, Producer $producer)
    {
        $this->em       = $em;
        $this->producer = $producer;
    }

    /**
     * Get repository.
     *
     * @return \AppBundle\Repository\AuthentificationRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('AppBundle:Authentification');
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    public function findAll(Project $project, $categorized = false)
    {
        $list = $this->getRepository()->findByProject($project->getId());
        if (!$categorized) {
            return $list;
        }

        $projects = [
            'repositories' => [],
            'servers'      => [],
        ];

        foreach ($list as $item) {
            if ($item->isRepository()) {
                $projects['repositories'][] = $item;
            } else {
                $projects['servers'][] = $item;
            }
        }

        return $projects;
    }

    /**
     * @param Authentification $authentification
     */
    public function save(Authentification $authentification)
    {
        if (null === $authentification->getId()) {
            $this->em->persist($authentification);
        }

        $this->em->flush();

        $this->checkAuthentification($authentification);
    }

    /**
     * @param Project $project
     * @param User    $user
     *
     * @return Authentification
     */
    public function create(Project $project, User $user, $type = null)
    {
        $authentification = new Authentification();
        $authentification->setProject($project);
        $authentification->setIsValid(false);
        $authentification->setIsChecked(false);
        $authentification->setUser($user);

        if ($type) {
            $authentification->setType($type);
        } else {
            $authentification->generateDefaultName();
        }

        return $authentification;
    }

    /**
     * @param $id
     *
     * @return Authentification
     */
    public function findOneById($id)
    {
        return $this->getRepository()->findOneById($id);
    }

    /**
     * @param Authentification $authentification
     */
    public function checkAuthentification(Authentification $authentification)
    {
        $authentification->setIsChecked(false);
        $this->em->flush();
        $this->producer->publish(serialize([
            'authentificationId' => $authentification->getId(),
        ]));
    }

    /**
     * @param Authentification $authentification
     */
    public function setIsValid(Authentification $authentification)
    {
        $authentification->setIsChecked(true);
        $authentification->setIsValid(true);

        $this->em->flush();
    }

    /**
     * @param Authentification $authentification
     */
    public function setIsInvalid(Authentification $authentification)
    {
        $authentification->setIsChecked(true);
        $authentification->setIsValid(false);

        $this->em->flush();
    }

    /**
     * @param Authentification $authentification
     */
    public function delete(Authentification $authentification)
    {
        $this->em->remove($authentification);
        $this->em->flush();
    }
}
